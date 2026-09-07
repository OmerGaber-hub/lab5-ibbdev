<?php

namespace App\Services;

use App\Contracts\ReputationServiceInterface;
use App\Models\Answer;

class ReputationService implements ReputationServiceInterface
{
    public function awardForAcceptedAnswer(Answer $answer): void
    {
        $answer->update(['is_accepted' => true]);
        $answer->user()->increment('reputation_points', 10);
    }
}