<?php

namespace App\Contracts;

use App\Models\Answer;

interface ReputationServiceInterface
{
    public function awardForAcceptedAnswer(Answer $answer): void;
}