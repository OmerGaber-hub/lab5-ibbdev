<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_answer_a_question(): void
    {
        $asker = User::factory()->create();
        $answerer = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $asker->id]);

        $response = $this->actingAs($answerer, 'sanctum')->postJson("/api/questions/{$question->id}/answers", [
            'body' => 'هذه هي الإجابة.',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.body', 'هذه هي الإجابة.');

        $this->assertDatabaseHas('answers', [
            'body' => 'هذه هي الإجابة.',
            'user_id' => $answerer->id,
            'question_id' => $question->id,
        ]);
    }

    public function test_cannot_answer_own_question(): void
    {
        $asker = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $asker->id]);

        $response = $this->actingAs($asker, 'sanctum')->postJson("/api/questions/{$question->id}/answers", [
            'body' => 'إجابة خاطئة.',
        ]);

        $response->assertStatus(403);
    }

    public function test_question_owner_can_accept_an_answer(): void
    {
        $asker = User::factory()->create();
        $answerer = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $asker->id]);
        $answer = Answer::factory()->create(['question_id' => $question->id, 'user_id' => $answerer->id]);

        $response = $this->actingAs($asker, 'sanctum')->postJson("/api/answers/{$answer->id}/accept");

        $response->assertStatus(200)
                 ->assertJsonPath('answer.is_accepted', true);

        $this->assertDatabaseHas('answers', [
            'id' => $answer->id,
            'is_accepted' => true,
        ]);
    }

    public function test_non_owner_cannot_accept_an_answer(): void
    {
        $asker = User::factory()->create();
        $answerer = User::factory()->create();
        $otherUser = User::factory()->create();

        $question = Question::factory()->create(['user_id' => $asker->id]);
        $answer = Answer::factory()->create(['question_id' => $question->id, 'user_id' => $answerer->id]);

        $response = $this->actingAs($otherUser, 'sanctum')->postJson("/api/answers/{$answer->id}/accept");

        $response->assertStatus(403);
    }
}
