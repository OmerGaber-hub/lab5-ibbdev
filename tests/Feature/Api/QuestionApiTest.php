<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_questions(): void
    {
        Question::factory(5)->create();

        $response = $this->getJson('/api/questions');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'body', 'author']
                     ],
                     'meta',
                     'links'
                 ]);
    }

    public function test_can_create_a_question(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/questions', [
            'title' => 'كيف أتعلم Laravel؟',
            'body' => 'أنا مبتدئ وأريد مساراً واضحاً.',
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.title', 'كيف أتعلم Laravel؟');

        $this->assertDatabaseHas('questions', [
            'title' => 'كيف أتعلم Laravel؟',
            'user_id' => $user->id,
        ]);
    }

    public function test_prevents_unauthenticated_users_from_creating_questions(): void
    {
        $response = $this->postJson('/api/questions', [
            'title' => 'Test',
            'body' => 'Test body',
        ]);

        $response->assertStatus(401);
    }

    public function test_can_update_own_question(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/questions/{$question->id}", [
            'title' => 'Updated Title',
            'body' => 'Updated Body',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.title', 'Updated Title');
    }

    public function test_cannot_update_someone_elses_question(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2, 'sanctum')->putJson("/api/questions/{$question->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
    }

    public function test_can_delete_own_question(): void
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/questions/{$question->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }
}
