<?php

namespace App\Services;

use App\Contracts\AnswerServiceInterface;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AnswerService implements AnswerServiceInterface
{
    public function create(User $user, Question $question, array $data): Answer
    {
        if ($question->user_id === $user->id) {
            throw ValidationException::withMessages([
                'body' => 'لا يمكنك الإجابة على سؤالك الخاص.',
            ]);
        }

        return Answer::create([
            'question_id' => $question->id,
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);
    }
}