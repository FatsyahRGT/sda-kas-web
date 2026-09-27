# Panduan Lengkap Graphify — SDA Kas Web

Dokumentasi ini menjelaskan cara kerja, penggunaan, dan pemeliharaan **Knowledge Graph Codebase (Graphify)** untuk repositori **SDA Kas Web**.

---

## 📌 1. Apa Itu Graphify?

**Graphify** adalah sistem pemetaan arsitektur kode dan *knowledge graph* yang mengubah seluruh file, class, relasi database, controller, middleware, dan views menjadi graf relasional yang dapat di-query, dianalisis, dan divisualisasikan.

### Status Graf Saat Ini:
* **Total Node**: `483 node` (Model, Controller, Service, Route, Migrasi, View, Config)
* **Total Edge (Keterhubungan)**: `968 relasi`
* **Komunitas Modul**: `64 cluster komunitas`
* **Akurasi Ekstraksi**: `100% Deterministic AST (Abstract Syntax Tree)`
* **Lokasi Data Graf**: `graphify-out/`

---

## 🏛️ 2. Arsitektur Inti (*God Nodes*)

Berdasarkan analisis graf, berikut adalah komponen inti dengan keterhubungan tertinggi dalam aplikasi:

| Komponen / Class | Jumlah Relasi (*Edges*) | Peran dalam Sistem |
|---|:---:|---|
| **`Group`** | 65 | Entitas utama grup kas / RT / unit kerja |
| **`User`** | 60 | Pengguna sistem & kontrol otentikasi peran (*RBAC*) |
| **`Period`** | 58 | Manajemen periode buku kas bulanan |
| **`Member`** | 31 | Data anggota kas per grup |
| **`Income`** | 25 | Pencatatan transaksi pemasukan / setoran |
| **`Expense`** | 22 | Pencatatan transaksi pengeluaran & bukti nota |
| **`ExpenseCategory`** | 17 | Klasifikasi kategori biaya pengeluaran |
| **`TestCase`** | 16 | Basis pengujian fitur dan unit |
| **`Controller`** | 14 | Base controller HTTP Laravel |
| **`UserController`** | 11 | Pengelolaan akun user & API token |

---

## ⚡ 3. Cara Kerja pada Sesi Baru (*Fast-Path*)

Ketika Anda memulai sesi percakapan atau sesi pengembangan baru dengan AI Agent:

1. **AI Tidak Perlu Scan Ulang**:  
   File `graphify-out/graph.json` bertindak sebagai *indeks memori permanen*.
2. **Respon Instan & Hemat Token**:  
   AI langsung melakukan traversal graf untuk menjawab pertanyaan alur logika kode tanpa membaca puluhan file manual satu per satu.

---

## 🛠️ 4. Perintah Query & Navigasi

Anda dapat menggunakan perintah Graphify berikut di terminal lokal atau melalui AI prompt:

### A. Menelusuri Alur Arsitektur (`query`)
Digunakan untuk menjawab pertanyaan menyeluruh tentang alur data atau fitur.
```powershell
graphify query "Bagaimana alur pencatatan pemasukan dari input form hingga laporan kas?"
```

### B. Mencari Jalur Hubungan Terpendek (`path`)
Digunakan untuk melihat bagaimana dua komponen yang terpisah saling berhubungan.
```powershell
graphify path "IncomeController" "Group"
graphify path "ArrearsService" "Member"
```

### C. Menjelaskan Komponen & Dependensinya (`explain`)
Digunakan untuk mendapatkan ringkasan menyeluruh mengenai suatu class atau modul.
```powershell
graphify explain "ArrearsService"
graphify explain "Group"
```

---

## 🔄 5. Pembaruan Bertahap (*Incremental Update*)

Jika Anda menambahkan controller baru, mengubah relasi model, atau membuat fitur baru:

```powershell
# Memperbarui graf hanya untuk file yang berubah (cepat & ringan)
graphify update .
```

*Graphify menggunakan hashing cache, sehingga hanya file yang baru diubah yang akan diekstrak ulang.*

---

## 🌐 6. Visualisasi Interaktif di Browser

Untuk melihat peta arsitektur aplikasi secara visual dalam bentuk grafik 2D/3D interaktif:

1. Buka file berikut di browser favorit Anda:
   ```
   graphify-out/graph.html
   ```
2. Fitur yang tersedia:
   * **Zoom & Pan**: Menjelajah jaringan modul aplikasi.
   * **Search**: Menemukan lokasi class / file tertentu secara instan.
   * **Cluster Highlighting**: Melihat pengelompokan modul berdasarkan komunitas logika.

---

## 📁 7. Struktur Direktori `graphify-out/`

```text
graphify-out/
├── graph.html           # File visualisasi interaktif mandiri (bisa dibuka di browser)
├── GRAPH_REPORT.md      # Laporan audit lengkap arsitektur, komunitas, & gap analisis
└── graph.json           # Raw dataset graf (node, edge, community metadata)
```

---

## 💡 8. Tips & Rekomendasi Pengembangan

* **Sebelum refactor modul besar**: Jalankan `graphify path` atau buka `graph.html` untuk memastikan tidak ada efek samping (*breaking dependencies*) pada controller atau service lain.
* **Audit Ketergantungan**: Periksa bagian `Knowledge Gaps` dan `Surprising Connections` di [graphify-out/GRAPH_REPORT.md](graphify-out/GRAPH_REPORT.md) untuk mendeteksi *dead code* atau dependensi yang janggal.
