<?php

namespace App\Http\Controllers;

use App\Contracts\AnswerServiceInterface;
use App\Contracts\ReputationServiceInterface;
use App\Http\Requests\StoreAnswerRequest;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Facades\Gate;

class AnswerController extends Controller
{
    public function __construct(
        protected AnswerServiceInterface $answerService,
        protected ReputationServiceInterface $reputationService
    ) {}

    public function store(StoreAnswerRequest $request, Question $question)
    {
        $this->answerService->create($request->user(), $question, $request->validated());

        return back()->with('success', 'تم إرسال إجابتك بنجاح.');
    }

    public function accept(Answer $answer)
    {
        Gate::authorize('accept', $answer);

        $this->reputationService->awardForAcceptedAnswer($answer);

        return back()->with('success', 'تم اعتماد الإجابة ومنح صاحبها 10 نقاط.');
    }
}