<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\User;

class AnswerPolicy
{
    public function accept(User $user, Answer $answer): bool
    {
        return $user->id === $answer->question->user_id;
    }
}