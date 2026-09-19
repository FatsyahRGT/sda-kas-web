<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\Group;
use App\Models\Member;
use App\Models\Period;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_groups(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create
        $response = $this->actingAs($admin)->post('/groups', [
            'name' => 'Kas Komunitas Baru',
            'slug' => 'kas-komunitas-baru',
            'description' => 'Deskripsi grup baru',
            'default_due_amount' => 30000,
            'is_public' => 1,
        ]);
        $response->assertRedirect('/groups');
        $this->assertDatabaseHas('groups', ['slug' => 'kas-komunitas-baru']);

        $group = Group::where('slug', 'kas-komunitas-baru')->first();

        // Update
        $this->actingAs($admin)->put("/groups/{$group->id}", [
            'name' => 'Kas Komunitas Diperbarui',
            'slug' => 'kas-komunitas-diperbarui',
            'default_due_amount' => 35000,
            'is_public' => 0,
        ]);
        $this->assertDatabaseHas('groups', ['slug' => 'kas-komunitas-diperbarui', 'is_public' => 0]);

        // Toggle Public
        $this->actingAs($admin)->post("/groups/{$group->id}/toggle-public");
        $this->assertDatabaseHas('groups', ['id' => $group->id, 'is_public' => 1]);

        // Delete
        $this->actingAs($admin)->delete("/groups/{$group->id}");
        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
    }

    public function test_admin_can_crud_members(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $group = Group::factory()->create(['created_by' => $admin->id]);

        // Create Member
        $this->actingAs($admin)->post('/members', [
            'group_id' => $group->id,
            'name' => 'Ahmad Dahlan',
            'phone' => '081299998888',
            'is_active' => 1,
        ]);
        $this->assertDatabaseHas('members', ['name' => 'Ahmad Dahlan']);

        $member = Member::where('name', 'Ahmad Dahlan')->first();

        // Update Member
        $this->actingAs($admin)->put("/members/{$member->id}", [
            'group_id' => $group->id,
            'name' => 'Ahmad Dahlan Update',
            'phone' => '081299997777',
            'is_active' => 0,
        ]);
        $this->assertDatabaseHas('members', ['name' => 'Ahmad Dahlan Update', 'is_active' => 0]);

        // Delete Member
        $this->actingAs($admin)->delete("/members/{$member->id}");
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }

    public function test_admin_can_crud_periods(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $group = Group::factory()->create(['created_by' => $admin->id]);

        // Create Period
        $this->actingAs($admin)->post('/periods', [
            'group_id' => $group->id,
            'month' => 5,
            'year' => 2026,
            'due_amount' => 60000,
            'status' => 'open',
        ]);
        $this->assertDatabaseHas('periods', ['month' => 5, 'year' => 2026, 'status' => 'open']);

        $period = Period::where('month', 5)->where('year', 2026)->first();

        // Update Status
        $this->actingAs($admin)->patch("/periods/{$period->id}/status", [
            'status' => 'closed',
        ]);
        $this->assertDatabaseHas('periods', ['id' => $period->id, 'status' => 'closed']);
    }

    public function test_admin_can_record_expense_with_attachment(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $group = Group::factory()->create(['created_by' => $admin->id]);
        $period = Period::factory()->create(['group_id' => $group->id, 'created_by' => $admin->id]);
        $category = ExpenseCategory::factory()->create();

        $file = UploadedFile::fake()->image('nota.jpg');

        $this->actingAs($admin)->post('/expenses', [
            'period_id' => $period->id,
            'category_id' => $category->id,
            'item_name' => 'Beli Spanduk',
            'nominal' => 75000,
            'transaction_date' => '2026-05-10 09:00',
            'attachments' => [$file],
        ]);

        $this->assertDatabaseHas('expenses', [
            'item_name' => 'Beli Spanduk',
            'nominal' => 75000,
        ]);

        $this->assertDatabaseCount('expense_attachments', 1);
    }

    public function test_admin_can_export_report_csv_and_view_print_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $group = Group::factory()->create(['created_by' => $admin->id]);
        $period = Period::factory()->create(['group_id' => $group->id, 'created_by' => $admin->id]);

        // CSV Export
        $response = $this->actingAs($admin)->get("/reports/{$period->id}/export-excel");
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        // Print Page
        $printResponse = $this->actingAs($admin)->get("/reports/{$period->id}/print");
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Laporan Rekapitulasi Kas');
    }
}
