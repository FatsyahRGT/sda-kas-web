Buatkan saya aplikasi manajemen uang kas berbasis Laravel dengan spesifikasi berikut:

## Konteks

Aplikasi ini untuk mengelola kas suatu grup (kas dari iuran anggota + pencatatan pengeluaran), meniru laporan
manual yang selama ini dibuat per bulan berisi: daftar setoran pemasukan per anggota, daftar pengeluaran per
barang/keperluan, dan ringkasan saldo. Aplikasi harus mendukung banyak grup kas dan banyak periode (bulan/tahun).

## Tech stack

- Laravel (versi LTS/terbaru)
- Dukungan PWA (manifest.json + service worker, installable, offline-first untuk halaman ringan)
- Autentikasi: Laravel Sanctum (sesi web admin panel + token API untuk akses otomasi)
- Admin web panel dengan sidebar navigasi berisi modul-modul manajemen (lihat daftar fitur di bawah)

## Role & akses

Ada 2 jenis user:

1. **admin** — akun manusia biasa, akses penuh ke admin panel (CRUD data, laporan, dsb) via login web.
2. **superadmin** — akun khusus untuk sistem otomasi eksternal saya mengakses aplikasi ini via API token (Sanctum),
   dengan hak akses setara atau lebih luas dari admin. Buat middleware role-based (admin vs superadmin), dan
   pastikan endpoint API otomasi dilindungi token superadmin, bukan sesi cookie.

## Struktur data (ERD)

Buat migration & model Eloquent untuk skema berikut:

- users (id, name, email, password, role[admin|superadmin], api_token nullable, is_active, timestamps)
- groups (id, name, slug unique, description, default_due_amount decimal, is_public boolean default false,
  created_by -> users.id, timestamps)
- members (id, group_id -> groups.id, name, phone nullable, is_active, timestamps)
- periods (id, group_id -> groups.id, month, year, due_amount decimal nullable (null = pakai groups.default_due_amount),
  status[draft|open|closed], created_by -> users.id, timestamps)
- incomes (id, period_id -> periods.id, member_id -> members.id, nominal decimal, transaction_date datetime,
  note nullable, created_by -> users.id, timestamps)
- expense_categories (id, name, timestamps)
- expenses (id, period_id -> periods.id, category_id -> expense_categories.id nullable, item_name, nominal decimal,
  transaction_date datetime, note nullable, created_by -> users.id, timestamps)
- expense_attachments (id, expense_id -> expenses.id, file_path, uploaded_at, timestamps)

Saldo per periode TIDAK disimpan sebagai kolom — hitung dinamis: SUM(incomes.nominal) - SUM(expenses.nominal)
untuk period_id terkait.

## Halaman publik (tanpa login)

Buat 1 halaman publik, hanya-lihat (read-only, tidak ada tombol edit/hapus sama sekali), yang menampilkan rekapan
per bulan untuk sebuah grup, dengan aturan:

- Diakses via URL berbasis slug grup, misalnya /publik/{group:slug} atau /publik/{group:slug}/{tahun}/{bulan}
- Hanya bisa diakses kalau groups.is_public = true; kalau false, tampilkan 404
- Isinya meniru format laporan lama: tabel Pemasukan (nama, nominal, tanggal), tabel Pengeluaran (nama barang,
  nominal, tanggal), ringkasan Balance/Sisa, dan galeri foto Dokumentasi Bukti Pengeluaran
- Ada dropdown/selector untuk pindah antar periode (bulan) yang berstatus "closed" (periode draft/open jangan
  ditampilkan ke publik)
- Tidak butuh middleware auth sama sekali, tapi tetap validasi is_public di controller/policy

## Dashboard admin (interaktif)

Selain ringkasan saldo & grafik pemasukan vs pengeluaran, tambahkan panel "Anggota menunggak" yang menghitung
untuk tiap member aktif di grup yang sedang dipilih:

- Total nominal yang harusnya dibayar sejauh ini vs total yang sudah dibayar (SUM incomes per member per periode
  dibandingkan periods.due_amount ?? groups.default_due_amount)
- Selisih/"minus" dalam rupiah, dan jumlah bulan yang belum lunas (hitung periode di mana total setoran member
  < due_amount periode tsb)
- Tabel ini interaktif: bisa di-sort (paling banyak nunggak di atas), difilter per grup/periode, dan idealnya
  pakai Livewire/AJAX supaya tidak perlu reload halaman penuh saat filter berubah
- Klik satu anggota bisa expand/drill-down menampilkan rincian bulan mana saja yang belum lunas

## Fitur sidebar admin panel

- Dashboard (ringkasan saldo, grafik, panel anggota menunggak di atas)
- Manajemen Grup Kas (CRUD groups, termasuk toggle is_public & atur slug)
- Manajemen Anggota (CRUD members per group)
- Manajemen Periode (buat/tutup periode bulanan, atur due_amount override kalau perlu)
- Pemasukan: input setoran per anggota per periode, list & filter
- Pengeluaran: input pengeluaran + upload multiple foto bukti, list & filter
- Kategori Pengeluaran (CRUD, opsional)
- Laporan: export laporan bulanan ke PDF/Excel
- Manajemen User (khusus superadmin: kelola akun admin, generate/revoke API token otomasi)

## Kebutuhan non-fungsional

- Validasi input di setiap form (nominal harus angka positif, tanggal valid, dsb)
- Middleware role admin/superadmin di setiap route group; halaman publik di luar middleware auth
- Seeder + factory untuk data dummy (groups, members, periods, incomes, expenses) agar mudah ditest, termasuk
  skenario anggota yang sengaja dibuat nunggak beberapa bulan
- Struktur folder mengikuti konvensi Laravel standar (Controllers, Requests, Policies)
- Manfaatkan skill "ponytail" yang sudah tersedia pada AI agent ini sesuai kapabilitasnya untuk mempercepat
  scaffolding proyek, jika relevan.

Tolong mulai dengan migration & model sesuai ERD di atas, lalu lanjutkan ke controller, routes, halaman publik,
panel dashboard tunggakan, dan sidebar admin panel.

## Struktur data (ERD, format ringkas)

users(id PK, name, email, password, role[admin|superadmin], api_token?, is_active)
groups(id PK, name, slug, description, default_due_amount, is_public, created_by->users.id)
members(id PK, group_id->groups.id, name, phone?, is_active)
periods(id PK, group_id->groups.id, month, year, due_amount?, status[draft|open|closed], created_by->users.id)
incomes(id PK, period_id->periods.id, member_id->members.id, nominal, transaction_date, note?, created_by->users.id)
expense_categories(id PK, name)
expenses(id PK, period_id->periods.id, category_id?->expense_categories.id, item_name, nominal, transaction_date, note?, created_by->users.id)
expense_attachments(id PK, expense_id->expenses.id, file_path, uploaded_at)

Catatan: "?" = nullable, "->" = foreign key. Saldo per periode tidak disimpan, hitung dinamis:
SUM(incomes.nominal) - SUM(expenses.nominal) per period_id.
