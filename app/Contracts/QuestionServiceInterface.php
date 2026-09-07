<?php

namespace App\Contracts;

use App\Models\Question;
use App\Models\User;

interface QuestionServiceInterface
{
    public function create(User $user, array $data): Question;
}