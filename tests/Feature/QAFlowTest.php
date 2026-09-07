<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QAFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_qa_flow()
    {
        // 1. Register User A (Question Asker)
        $response = $this->post('/register', [
            'name' => 'Ahmed',
            'username' => 'ahmed',
            'email' => 'ahmed@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertRedirect(route('questions.index'));
        $userA = User::where('email', 'ahmed@test.com')->first();
        $this->assertNotNull($userA);

        // 2. User A posts a question
        $this->actingAs($userA);
        $response = $this->post('/questions', [
            'title' => 'How to learn Laravel?',
            'body' => 'I want to learn Laravel. What are the best resources?',
        ]);
        $response->assertRedirect(route('questions.index'));
        
        $question = Question::first();
        $this->assertNotNull($question);
        $this->assertEquals('How to learn Laravel?', $question->title);

        // 3. Register User B (Answerer)
        $this->post('/logout');
        
        $this->post('/register', [
            'name' => 'Ali',
            'username' => 'ali',
            'email' => 'ali@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $userB = User::where('email', 'ali@test.com')->first();
        $this->assertNotNull($userB);

        // 4. User B posts an answer to User A's question
        $this->actingAs($userB);
        $response = $this->post("/questions/{$question->id}/answers", [
            'body' => 'You should check out Laracasts.',
        ], ['Referer' => route('questions.show', $question)]);
        
        $response->assertRedirect(route('questions.show', $question));
        
        $answer = Answer::first();
        $this->assertNotNull($answer);
        $this->assertEquals('You should check out Laracasts.', $answer->body);

        // 5. User A accepts the answer
        $this->actingAs($userA);
        $response = $this->post("/answers/{$answer->id}/accept", [], ['Referer' => route('questions.show', $question)]);
        $response->assertRedirect(route('questions.show', $question));

        // 6. Verify reputation points are awarded to User B
        $answer->refresh();
        $userB->refresh();
        
        $this->assertTrue((bool) $answer->is_accepted);
        $this->assertEquals(10, $userB->reputation_points);
    }
}
