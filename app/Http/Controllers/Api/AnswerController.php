<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnswerResource;
use App\Models\Answer;
use App\Models\Question;
use App\Services\ReputationService;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Question $question)
    {
        if ($request->user()->id === $question->user_id) {
            abort(403, 'لا يمكنك الإجابة على سؤالك الخاص.');
        }

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $answer = $question->answers()->create([
            'body' => $validated['body'],
            'user_id' => $request->user()->id,
        ]);

        return new AnswerResource($answer->load('user'));
    }

    /**
     * Accept the answer as the solution.
     */
    public function accept(Request $request, Answer $answer, ReputationService $reputationService)
    {
        $question = $answer->question;

        if ($request->user()->cannot('accept', $answer)) {
            abort(403, 'غير مصرح لك باعتماد الإجابة، يجب أن تكون صاحب السؤال.');
        }

        if ($answer->is_accepted) {
            return response()->json(['message' => 'هذه الإجابة معتمدة مسبقاً'], 400);
        }

        // Remove acceptance from other answers for this question
        $question->answers()->where('is_accepted', true)->update(['is_accepted' => false]);
        
        $answer->update(['is_accepted' => true]);

        // Award reputation points using the service
        $reputationService->awardForAcceptedAnswer($answer);

        return response()->json([
            'message' => 'تم اعتماد الإجابة بنجاح',
            'answer' => new AnswerResource($answer)
        ]);
    }
}
