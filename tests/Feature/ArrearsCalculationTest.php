<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Income;
use App\Models\Member;
use App\Models\Period;
use App\Models\User;
use App\Services\ArrearsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArrearsCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_arrears_service_calculates_unpaid_months_and_shortages_accurately(): void
    {
        $user = User::factory()->create();
        $group = Group::factory()->create([
            'default_due_amount' => 50000,
            'created_by' => $user->id,
        ]);

        $memberPaid = Member::factory()->create(['group_id' => $group->id, 'name' => 'Member Lunas']);
        $memberLate = Member::factory()->create(['group_id' => $group->id, 'name' => 'Member Nunggak 2 Bulan']);
        $memberPartial = Member::factory()->create(['group_id' => $group->id, 'name' => 'Member Bayar Sebagian']);

        // 3 active periods
        $p1 = Period::factory()->create(['group_id' => $group->id, 'month' => 1, 'year' => 2026, 'status' => 'closed', 'created_by' => $user->id]);
        $p2 = Period::factory()->create(['group_id' => $group->id, 'month' => 2, 'year' => 2026, 'status' => 'closed', 'created_by' => $user->id]);
        $p3 = Period::factory()->create(['group_id' => $group->id, 'month' => 3, 'year' => 2026, 'status' => 'open', 'created_by' => $user->id]);

        // Member Lunas: pays 50k in all 3 periods
        foreach ([$p1, $p2, $p3] as $p) {
            Income::factory()->create([
                'period_id' => $p->id,
                'member_id' => $memberPaid->id,
                'nominal' => 50000,
                'created_by' => $user->id,
            ]);
        }

        // Member Late: only pays in p1 (misses p2 & p3 = 2 unpaid months, 100k shortage)
        Income::factory()->create([
            'period_id' => $p1->id,
            'member_id' => $memberLate->id,
            'nominal' => 50000,
            'created_by' => $user->id,
        ]);

        // Member Partial: pays 30k in p1, 0 in p2, 50k in p3 = 2 unpaid months (p1 short 20k, p2 short 50k = 70k total shortage)
        Income::factory()->create([
            'period_id' => $p1->id,
            'member_id' => $memberPartial->id,
            'nominal' => 30000,
            'created_by' => $user->id,
        ]);
        Income::factory()->create([
            'period_id' => $p3->id,
            'member_id' => $memberPartial->id,
            'nominal' => 50000,
            'created_by' => $user->id,
        ]);

        $service = new ArrearsService;
        $results = $service->getArrears($group->id);

        $rowPaid = $results->firstWhere('member_id', $memberPaid->id);
        $this->assertEquals(0, $rowPaid['total_shortage']);
        $this->assertEquals(0, $rowPaid['unpaid_months_count']);
        $this->assertFalse($rowPaid['has_arrears']);

        $rowLate = $results->firstWhere('member_id', $memberLate->id);
        $this->assertEquals(100000, $rowLate['total_shortage']);
        $this->assertEquals(2, $rowLate['unpaid_months_count']);
        $this->assertTrue($rowLate['has_arrears']);
        $this->assertCount(2, $rowLate['unpaid_periods']);

        $rowPartial = $results->firstWhere('member_id', $memberPartial->id);
        $this->assertEquals(70000, $rowPartial['total_shortage']);
        $this->assertEquals(2, $rowPartial['unpaid_months_count']);
    }

    public function test_dashboard_arrears_ajax_endpoint_returns_json(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $group = Group::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->getJson('/dashboard/arrears-data?group_id='.$group->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
        ]);
    }
}
