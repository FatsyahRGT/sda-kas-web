<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_admin_cannot_access_user_management(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(403);
    }

    public function test_superadmin_can_access_user_management(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($superadmin)->get('/users');
        $response->assertStatus(200);
        $response->assertSee('Manajemen User & API Token');
    }

    public function test_superadmin_can_create_new_admin_user(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($superadmin)->post('/users', [
            'name' => 'Bendahara Dua',
            'email' => 'bendahara2@kas.test',
            'password' => 'password123',
            'role' => 'admin',
            'is_active' => 1,
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'bendahara2@kas.test', 'role' => 'admin']);
    }

    public function test_superadmin_can_generate_and_revoke_api_tokens(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $targetUser = User::factory()->create(['role' => 'superadmin', 'name' => 'Bot Otomasi']);

        // Generate Token
        $response = $this->actingAs($superadmin)->post("/users/{$targetUser->id}/generate-token");
        $response->assertSessionHas('plainTextToken');
        $this->assertNotEmpty($targetUser->fresh()->tokens);

        // Revoke Token
        $revokeResponse = $this->actingAs($superadmin)->post("/users/{$targetUser->id}/revoke-tokens");
        $revokeResponse->assertSessionHas('success');
        $this->assertCount(0, $targetUser->fresh()->tokens);
    }
}
