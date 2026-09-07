<?php

namespace App\Contracts;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;

interface AnswerServiceInterface
{
    public function create(User $user, Question $question, array $data): Answer;
}