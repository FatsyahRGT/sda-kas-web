<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Group;
use App\Models\Income;
use App\Models\Member;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicKasTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_group_page_is_accessible_without_login(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create([
            'name' => 'Kas Warga Terbuka',
            'slug' => 'kas-warga-terbuka',
            'is_public' => true,
            'created_by' => $user->id,
        ]);

        $periodClosed = Period::factory()->create([
            'group_id' => $group->id,
            'month' => 1,
            'year' => 2026,
            'status' => 'closed',
            'created_by' => $user->id,
        ]);

        $response = $this->get('/publik/kas-warga-terbuka');

        $response->assertStatus(200);
        $response->assertSee('Kas Warga Terbuka');
        $response->assertSee('Januari 2026');
    }

    public function test_private_group_page_returns_404(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create([
            'name' => 'Kas Tertutup',
            'slug' => 'kas-tertutup',
            'is_public' => false,
            'created_by' => $user->id,
        ]);

        $response = $this->get('/publik/kas-tertutup');
        $response->assertStatus(404);
    }

    public function test_draft_or_open_period_is_not_displayed_on_public_page(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create([
            'slug' => 'kas-warga',
            'is_public' => true,
            'created_by' => $user->id,
        ]);

        Period::factory()->create([
            'group_id' => $group->id,
            'month' => 2,
            'year' => 2026,
            'status' => 'open',
            'created_by' => $user->id,
        ]);

        $response = $this->get('/publik/kas-warga');
        $response->assertStatus(200);
        // Should show empty/no closed period banner
        $response->assertSee('Belum Ada Laporan Kas yang Dipublikasikan');
    }

    public function test_public_page_shows_incomes_and_expenses_for_closed_period(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create([
            'slug' => 'kas-transparan',
            'is_public' => true,
            'created_by' => $user->id,
        ]);

        $period = Period::factory()->create([
            'group_id' => $group->id,
            'month' => 1,
            'year' => 2026,
            'status' => 'closed',
            'created_by' => $user->id,
        ]);

        $member = Member::factory()->create([
            'group_id' => $group->id,
            'name' => 'Budi Sudarsono',
        ]);

        Income::factory()->create([
            'period_id' => $period->id,
            'member_id' => $member->id,
            'nominal' => 75000,
            'transaction_date' => now(),
            'created_by' => $user->id,
        ]);

        Expense::factory()->create([
            'period_id' => $period->id,
            'item_name' => 'Beli Alat Sapu',
            'nominal' => 25000,
            'transaction_date' => now(),
            'created_by' => $user->id,
        ]);

        $response = $this->get('/publik/kas-transparan');

        $response->assertStatus(200);
        $response->assertSee('Budi Sudarsono');
        $response->assertSee('75.000');
        $response->assertSee('Beli Alat Sapu');
        $response->assertSee('25.000');
        // Net balance: 75000 - 25000 = 50000
        $response->assertSee('50.000');
    }
}
