# Panduan Deploy Docker ke VPS (43.157.243.62)

## Informasi Server VPS & Database
- **Host / IP**: `43.157.243.62`
- **SSH User**: `ubuntu`
- **Direktori Aplikasi**: `~/vps-infra` (atau `/home/ubuntu/vps-infra`)
- **MySQL Root Password**: `t3g4lr3j0`
- **MySQL Database**: `myfinance_db`
- **MySQL User**: `frgtx`
- **MySQL Password**: `asddsa123`

---

## Langkah 1: Upload Source Code ke VPS

Dari terminal komputer lokal Anda (Git Bash / PowerShell):

```bash
# Upload project ke folder vps-infra di VPS
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' ./ ubuntu@43.157.243.62:~/vps-infra/
```

*Atau jika menggunakan Git di VPS:*
```bash
ssh ubuntu@43.157.243.62
git clone <URL_REPO_ANDA> vps-infra
cd vps-infra
```

---

## Langkah 2: Jalankan Deployment di VPS

Masuk ke VPS via SSH:
```bash
ssh ubuntu@43.157.243.62
cd ~/vps-infra
```

Jalankan container dengan perintah:
```bash
docker compose up -d --build
```
*Atau gunakan script otomatis:*
```bash
chmod +x vps-infra/deploy.sh
./vps-infra/deploy.sh
```

---

## Langkah 3: Seed Data Awal (Opsional)

Jika ingin mengisi database baru dengan akun admin, superadmin, dan contoh data grup:
```bash
docker compose exec app php artisan db:seed --force
```

**Kredensial Default Setelah Seeder:**
- **Superadmin**: `superadmin@kas.test` / Password: `password`
- **Admin**: `admin@kas.test` / Password: `password`

---

## Langkah 4: Akses Aplikasi
Buka di browser:
👉 **`http://43.157.243.62`**
👉 **`http://43.157.243.62/login`**
👉 **`http://43.157.243.62/publik/kas-warga-rt-04`**

---

## Perintah Manajemen yang Berguna

- **Lihat Log Aplikasi & Database:**
  ```bash
  docker compose logs -f app
  docker compose logs -f db
  ```
- **Masuk ke CLI Container Laravel:**
  ```bash
  docker compose exec app sh
  ```
- **Restart Container:**
  ```bash
  docker compose restart
  ```
- **Hentikan Container:**
  ```bash
  docker compose down
  ```
