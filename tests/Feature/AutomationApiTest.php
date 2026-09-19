<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Member;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AutomationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_api_request_returns_unauthorized(): void
    {
        $response = $this->getJson('/api/v1/summary');
        $response->assertStatus(401);
    }

    public function test_admin_user_cannot_access_superadmin_api_endpoints(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/v1/summary');
        $response->assertStatus(403);
    }

    public function test_superadmin_with_sanctum_token_can_access_automation_api(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        Sanctum::actingAs($superadmin, ['*']);

        $response = $this->getJson('/api/v1/summary');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'total_groups',
                'total_members',
                'total_periods',
                'total_income',
                'total_expense',
                'total_balance',
            ],
        ]);
    }

    public function test_superadmin_can_record_income_via_api(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        Sanctum::actingAs($superadmin, ['*']);

        $group = Group::factory()->create(['created_by' => $superadmin->id]);
        $period = Period::factory()->create(['group_id' => $group->id, 'created_by' => $superadmin->id]);
        $member = Member::factory()->create(['group_id' => $group->id]);

        $response = $this->postJson('/api/v1/incomes', [
            'period_id' => $period->id,
            'member_id' => $member->id,
            'nominal' => 50000,
            'transaction_date' => '2026-03-15 10:00:00',
            'note' => 'Setoran via Bot Otomasi Telegram',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.nominal', '50000.00');

        $this->assertDatabaseHas('incomes', [
            'period_id' => $period->id,
            'member_id' => $member->id,
            'nominal' => 50000,
            'created_by' => $superadmin->id,
        ]);
    }

    public function test_superadmin_can_record_expense_via_api(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        Sanctum::actingAs($superadmin, ['*']);

        $group = Group::factory()->create(['created_by' => $superadmin->id]);
        $period = Period::factory()->create(['group_id' => $group->id, 'created_by' => $superadmin->id]);

        $response = $this->postJson('/api/v1/expenses', [
            'period_id' => $period->id,
            'item_name' => 'Beli Token Listrik Balai Warga',
            'nominal' => 100000,
            'transaction_date' => '2026-03-16 12:00:00',
            'note' => 'Otomasi PLN API',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('expenses', [
            'period_id' => $period->id,
            'item_name' => 'Beli Token Listrik Balai Warga',
            'nominal' => 100000,
        ]);
    }
}
