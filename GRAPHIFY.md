# Panduan Lengkap Graphify — SDA Kas Web

Dokumentasi ini menjelaskan cara kerja, penggunaan, dan pemeliharaan **Knowledge Graph Codebase (Graphify)** untuk repositori **SDA Kas Web**.

---

## 📌 1. Apa Itu Graphify?

**Graphify** adalah sistem pemetaan arsitektur kode dan *knowledge graph* yang mengubah seluruh file, class, relasi database, controller, middleware, dan views menjadi graf relasional yang dapat di-query, dianalisis, dan divisualisasikan.

### Status Graf Saat Ini:
* **Total Node**: `509 node` (Model, Controller, Service, Route, Migrasi, View, Config)
* **Total Edge (Keterhubungan)**: `1009 relasi`
* **Komunitas Modul**: `57 cluster komunitas`
* **Akurasi Ekstraksi**: `100% Deterministic AST (Abstract Syntax Tree)`
* **Lokasi Data Graf**: `graphify-out/` (terlacak di git: `graph.json`, `GRAPH_REPORT.md`, `manifest.json`)

---

## 🚀 2. Panduan Penggunaan Lengkap

### A. Ketika Baru Memulai Project (Cold Start / Clone Baru)
Jika project baru di-clone ke komputer baru atau berganti model AI (Gemini, Claude, GPT, dll):

1. **Instalasi Graphify CLI (jika belum ada)**:
   ```powershell
   pip install --upgrade graphifyy
   ```
2. **Tidak Perlu Scanning Ulang dari Nol (*Fast Path*)**:
   Karena `graphify-out/graph.json` sudah tersimpan di Git repo, AI atau developer bisa langsung menjalankan query arsitektur tanpa waktu tunggu scan:
   ```powershell
   graphify query "Bagaimana arsitektur controller dan relasi data di project ini?"
   ```
3. **Jika Ingin Melakukan Full Rebuild Graf dari Awal**:
   ```powershell
   graphify .
   ```

---

### B. Ketika Ada Penambahan File Baru atau Perubahan Kode (*Incremental Update*)
Jika Anda menambah Model baru, Controller baru, Service baru, atau mengubah kode:

1. **Jalankan Incremental Update**:
   ```powershell
   graphify . --update
   ```
   *Graphify hanya akan memproses file-file yang baru atau diubah saja (menggunakan hash manifest), sehingga proses update selesai dalam 1-2 detik.*

2. **Ekspor Ulang Visualisasi Interaktif (Opsional)**:
   ```powershell
   graphify export html
   ```

3. **Commit Perubahan Graf ke Git**:
   ```powershell
   git add graphify-out/graph.json graphify-out/GRAPH_REPORT.md graphify-out/manifest.json
   git commit -m "chore: update graphify knowledge graph"
   ```

---

## 🛠️ 3. Perintah Query & Navigasi

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

## 🏛️ 4. Arsitektur Inti (*God Nodes*)

Berdasarkan analisis graf, berikut adalah komponen inti dengan keterhubungan tertinggi dalam aplikasi:

| Komponen / Class | Jumlah Relasi (*Edges*) | Peran dalam Sistem |
|---|:---:|---|
| **`Group`** | 65 | Entitas utama grup kas / RT / unit kerja |
| **`User`** | 60 | Pengguna sistem & kontrol otentikasi peran (*RBAC*) |
| **`Period`** | 58 | Manajemen periode buku kas bulanan |
| **`Member`** | 33 | Data anggota kas per grup |
| **`Income`** | 27 | Pencatatan transaksi pemasukan / setoran |
| **`Expense`** | 24 | Pencatatan transaksi pengeluaran & bukti nota |
| **`ExpenseCategory`** | 17 | Klasifikasi kategori biaya pengeluaran |
| **`TestCase`** | 16 | Basis pengujian fitur dan unit |
| **`ArrearsService`** | 15 | Perhitungan tunggakan dan kepatuhan kas |
| **`Controller`** | 14 | Base controller HTTP Laravel |

---

## 🌐 5. Visualisasi Interaktif di Browser

Untuk melihat peta arsitektur aplikasi secara visual dalam bentuk grafik 2D/3D interaktif:

1. Buka file berikut di browser:
   ```
   graphify-out/graph.html
   ```
2. Fitur yang tersedia:
   * **Zoom & Pan**: Menjelajah jaringan modul aplikasi.
   * **Search**: Menemukan lokasi class / file tertentu secara instan.
   * **Cluster Highlighting**: Melihat pengelompokan modul berdasarkan komunitas logika.

---

## 📁 6. Struktur Direktori `graphify-out/`

```text
graphify-out/
├── graph.html           # Visualisasi interaktif mandiri (bisa dibuka di browser)
├── GRAPH_REPORT.md      # Laporan audit lengkap arsitektur, komunitas, & gap analisis (tracked di Git)
├── graph.json           # Raw dataset graf: 509 nodes, 1009 edges (tracked di Git)
├── manifest.json        # File cache fingerprint untuk update cepat (tracked di Git)
├── cost.json            # Log token tracking (tracked di Git)
└── cache/               # AST cache internal (di-ignore oleh .gitignore)
```
