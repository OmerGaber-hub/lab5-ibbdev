<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_a_new_user_via_api(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'API User',
            'username' => 'api_user',
            'email' => 'api@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'user' => ['id', 'name', 'username', 'email'],
                     'token',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'api@example.com',
        ]);
    }

    public function test_can_login_an_existing_user_via_api(): void
    {
        $user = User::factory()->create([
            'email' => 'testapi@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'testapi@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'user',
                     'token',
                 ]);
    }

    public function test_can_logout_an_authenticated_user(): void
    {
        $user = User::factory()->create();
        
        \Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'تم تسجيل الخروج بنجاح',
                 ]);
    }
}
