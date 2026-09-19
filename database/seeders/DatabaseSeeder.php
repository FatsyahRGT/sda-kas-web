<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\ExpenseAttachment;
use App\Models\ExpenseCategory;
use App\Models\Group;
use App\Models\Income;
use App\Models\Member;
use App\Models\Period;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@kas.test'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );

        // Generate Sanctum Token for automation testing
        $superadmin->tokens()->delete();
        $token = $superadmin->createToken('superadmin-automation-token', ['*']);
        $superadmin->update(['api_token' => hash('sha256', $token->plainTextToken)]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@kas.test'],
            [
                'name' => 'Bendahara Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Expense Categories
        $categories = [
            'Konsumsi & Rapat',
            'Kebersihan & Sanitasi',
            'Peralatan & Fasilitas',
            'Sosial & Santunan',
            'Operasional & Lainnya',
        ];

        $categoryModels = [];
        foreach ($categories as $catName) {
            $categoryModels[$catName] = ExpenseCategory::firstOrCreate(['name' => $catName]);
        }

        // 3. Groups
        $groupRt = Group::firstOrCreate(
            ['slug' => 'kas-warga-rt-04'],
            [
                'name' => 'Kas Warga RT 04 Sukamaju',
                'description' => 'Iuran bulanan warga RT 04 untuk kebersihan, keamanan, dan kegiatan sosial warga.',
                'default_due_amount' => 50000,
                'is_public' => true,
                'created_by' => $admin->id,
            ]
        );

        $groupPemuda = Group::firstOrCreate(
            ['slug' => 'kas-pemuda-kreatif'],
            [
                'name' => 'Kas Pemuda Kreatif',
                'description' => 'Kas organisasi kepemudaan untuk program olahraga dan pelatihan.',
                'default_due_amount' => 25000,
                'is_public' => false,
                'created_by' => $admin->id,
            ]
        );

        // 4. Members for RT 04
        $memberNames = [
            ['name' => 'Budi Santoso', 'phone' => '081234567801'],
            ['name' => 'Siti Rahma', 'phone' => '081234567802'],
            ['name' => 'Agus Pratama', 'phone' => '081234567803'],
            ['name' => 'Hendra Wijaya', 'phone' => '081234567804'],
            ['name' => 'Dewi Lestari', 'phone' => '081234567805'],
            ['name' => 'Rian Hidayat', 'phone' => '081234567806'],
            ['name' => 'Maya Safitri', 'phone' => '081234567807'],
            ['name' => 'Joko Susilo', 'phone' => '081234567808'],
            ['name' => 'Eko Prasetyo', 'phone' => '081234567809'],
            ['name' => 'Rudi Hartono', 'phone' => '081234567810'],
        ];

        $members = [];
        foreach ($memberNames as $m) {
            $members[$m['name']] = Member::firstOrCreate(
                ['group_id' => $groupRt->id, 'name' => $m['name']],
                ['phone' => $m['phone'], 'is_active' => true]
            );
        }

        // 5. Periods for RT 04
        $pJan = Period::firstOrCreate(
            ['group_id' => $groupRt->id, 'year' => 2026, 'month' => 1],
            ['status' => 'closed', 'due_amount' => 50000, 'created_by' => $admin->id]
        );

        $pFeb = Period::firstOrCreate(
            ['group_id' => $groupRt->id, 'year' => 2026, 'month' => 2],
            ['status' => 'closed', 'due_amount' => 50000, 'created_by' => $admin->id]
        );

        $pMar = Period::firstOrCreate(
            ['group_id' => $groupRt->id, 'year' => 2026, 'month' => 3],
            ['status' => 'open', 'due_amount' => 50000, 'created_by' => $admin->id]
        );

        // 6. Incomes (with deliberate arrears scenarios)
        // Scenario 1: Always on time (Budi, Siti, Rian, Joko, Eko, Rudi)
        $punctual = ['Budi Santoso', 'Siti Rahma', 'Rian Hidayat', 'Joko Susilo', 'Eko Prasetyo', 'Rudi Hartono'];
        foreach ($punctual as $name) {
            Income::firstOrCreate(
                ['period_id' => $pJan->id, 'member_id' => $members[$name]->id],
                ['nominal' => 50000, 'transaction_date' => Carbon::create(2026, 1, 5, 10, 0), 'note' => 'Lunas via Transfer', 'created_by' => $admin->id]
            );
            Income::firstOrCreate(
                ['period_id' => $pFeb->id, 'member_id' => $members[$name]->id],
                ['nominal' => 50000, 'transaction_date' => Carbon::create(2026, 2, 6, 11, 0), 'note' => 'Lunas Tunai', 'created_by' => $admin->id]
            );
            Income::firstOrCreate(
                ['period_id' => $pMar->id, 'member_id' => $members[$name]->id],
                ['nominal' => 50000, 'transaction_date' => Carbon::create(2026, 3, 4, 14, 30), 'note' => 'Lunas via Transfer', 'created_by' => $admin->id]
            );
        }

        // Scenario 2: 1 month behind (Agus Pratama -> paid Jan, Feb; missed Mar)
        Income::firstOrCreate(
            ['period_id' => $pJan->id, 'member_id' => $members['Agus Pratama']->id],
            ['nominal' => 50000, 'transaction_date' => Carbon::create(2026, 1, 10, 9, 0), 'created_by' => $admin->id]
        );
        Income::firstOrCreate(
            ['period_id' => $pFeb->id, 'member_id' => $members['Agus Pratama']->id],
            ['nominal' => 50000, 'transaction_date' => Carbon::create(2026, 2, 12, 9, 30), 'created_by' => $admin->id]
        );

        // Scenario 3: 2 months behind (Hendra Wijaya -> paid Jan; missed Feb & Mar)
        Income::firstOrCreate(
            ['period_id' => $pJan->id, 'member_id' => $members['Hendra Wijaya']->id],
            ['nominal' => 50000, 'transaction_date' => Carbon::create(2026, 1, 15, 16, 0), 'created_by' => $admin->id]
        );

        // Scenario 4: 3 months behind (Dewi Lestari -> missed Jan, Feb, Mar = Rp 150.000 arrears)
        // No incomes created for Dewi!

        // Scenario 5: Partial payment (Maya Safitri -> paid Rp 30.000 in Jan, Rp 50.000 in Feb, missed Mar)
        Income::firstOrCreate(
            ['period_id' => $pJan->id, 'member_id' => $members['Maya Safitri']->id],
            ['nominal' => 30000, 'transaction_date' => Carbon::create(2026, 1, 8, 11, 0), 'note' => 'Bayar sebagian', 'created_by' => $admin->id]
        );
        Income::firstOrCreate(
            ['period_id' => $pFeb->id, 'member_id' => $members['Maya Safitri']->id],
            ['nominal' => 50000, 'transaction_date' => Carbon::create(2026, 2, 10, 10, 0), 'created_by' => $admin->id]
        );

        // 7. Expenses
        // Create sample placeholder image in public storage for documentation test
        Storage::disk('public')->makeDirectory('attachments');
        $sampleSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300"><rect width="400" height="300" fill="#f1f5f9"/><rect x="20" y="20" width="360" height="260" rx="8" fill="#ffffff" stroke="#cbd5e1" stroke-width="2"/><text x="200" y="140" text-anchor="middle" font-family="sans-serif" font-size="20" font-weight="bold" fill="#334155">BUKTI NOTA KAS</text><text x="200" y="175" text-anchor="middle" font-family="sans-serif" font-size="14" fill="#64748b">Dokumentasi Pembelian</text></svg>';
        Storage::disk('public')->put('attachments/sample-receipt.svg', $sampleSvg);

        $exp1 = Expense::firstOrCreate(
            ['period_id' => $pJan->id, 'item_name' => 'Beli Lampu Sorot Pos Kamling'],
            [
                'category_id' => $categoryModels['Peralatan & Fasilitas']->id,
                'nominal' => 120000,
                'transaction_date' => Carbon::create(2026, 1, 12, 14, 0),
                'note' => '2 unit lampu LED Philips 20W',
                'created_by' => $admin->id,
            ]
        );
        ExpenseAttachment::firstOrCreate(
            ['expense_id' => $exp1->id, 'file_path' => 'attachments/sample-receipt.svg']
        );

        $exp2 = Expense::firstOrCreate(
            ['period_id' => $pJan->id, 'item_name' => 'Sapu & Karung Kerja Bakti'],
            [
                'category_id' => $categoryModels['Kebersihan & Sanitasi']->id,
                'nominal' => 85000,
                'transaction_date' => Carbon::create(2026, 1, 20, 8, 0),
                'note' => 'Beli di Toko Bangunan Berkah',
                'created_by' => $admin->id,
            ]
        );
        ExpenseAttachment::firstOrCreate(
            ['expense_id' => $exp2->id, 'file_path' => 'attachments/sample-receipt.svg']
        );

        $exp3 = Expense::firstOrCreate(
            ['period_id' => $pFeb->id, 'item_name' => 'Konsumsi Rapat Warga'],
            [
                'category_id' => $categoryModels['Konsumsi & Rapat']->id,
                'nominal' => 150000,
                'transaction_date' => Carbon::create(2026, 2, 14, 19, 30),
                'note' => 'Snack dan air mineral 50 porsi',
                'created_by' => $admin->id,
            ]
        );

        $exp4 = Expense::firstOrCreate(
            ['period_id' => $pFeb->id, 'item_name' => 'Santunan Warga Sakit (Bpk. Joko)'],
            [
                'category_id' => $categoryModels['Sosial & Santunan']->id,
                'nominal' => 200000,
                'transaction_date' => Carbon::create(2026, 2, 22, 16, 0),
                'note' => 'Diserahkan oleh Pengurus RT',
                'created_by' => $admin->id,
            ]
        );

        $exp5 = Expense::firstOrCreate(
            ['period_id' => $pMar->id, 'item_name' => 'Cat & Kuas Pos Ronda'],
            [
                'category_id' => $categoryModels['Peralatan & Fasilitas']->id,
                'nominal' => 95000,
                'transaction_date' => Carbon::create(2026, 3, 10, 10, 0),
                'note' => 'Cat kayu dan besi warna biru',
                'created_by' => $admin->id,
            ]
        );
        ExpenseAttachment::firstOrCreate(
            ['expense_id' => $exp5->id, 'file_path' => 'attachments/sample-receipt.svg']
        );
    }
}
