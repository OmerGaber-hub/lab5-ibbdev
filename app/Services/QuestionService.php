<?php

namespace App\Services;

use App\Contracts\QuestionServiceInterface;
use App\Models\Question;
use App\Models\User;

class QuestionService implements QuestionServiceInterface
{
    public function create(User $user, array $data): Question
    {
        $imagePath = null;

        if (isset($data['image'])) {
            $imagePath = $data['image']->store('questions', 'public');
        }

        return Question::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'body' => $data['body'],
            'image' => $imagePath,
        ]);
    }
}