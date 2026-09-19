# Panduan Deploy Docker ke VPS (43.157.243.62)

## Informasi Server VPS & Database
- **Host / IP**: `43.157.243.62`
- **Port Aplikasi**: `8081`
- **SSH User**: `ubuntu`
- **Direktori Aplikasi**: `~/web-apps/vps-infra` (atau `~/vps-infra`)
- **MySQL Root Password**: `t3g4lr3j0`
- **MySQL Database**: `myfinance_db`
- **MySQL User**: `frgtx`
- **MySQL Password**: `asddsa123`
- **MySQL Host Port**: `3307`
- **MySQL Volume**: `sda_kas_mysql_data`

---

## Langkah 1: Clone atau Update di VPS

Masuk ke VPS via SSH:
```bash
ssh ubuntu@43.157.243.62
mkdir -p ~/web-apps
cd ~/web-apps
```

Jika baru pertama kali:
```bash
git clone https://github.com/FatsyahRGT/sda-kas-web.git vps-infra
cd vps-infra
```

Jika sudah ada:
```bash
cd ~/web-apps/vps-infra
git pull origin main
```

---

## Langkah 2: Build & Jalankan Docker Container

```bash
docker compose up -d --build
```

*(Script entrypoint akan otomatis menyalin `.env.example`, generate `APP_KEY`, set permission 777 untuk storage, menunggu MySQL, dan menjalankan migrasi database)*

---

## Langkah 3: Seed Data Awal (Opsional)

Untuk mengisi database dengan akun default dan contoh data:
```bash
docker compose exec app php artisan db:seed --force
```

**Kredensial Default:**
- **Superadmin**: `superadmin@kas.test` / Password: `password`
- **Admin**: `admin@kas.test` / Password: `password`

---

## Langkah 4: Akses Aplikasi
Buka di browser:
👉 **`http://43.157.243.62:8081`**
👉 **`http://43.157.243.62:8081/login`**
👉 **`http://43.157.243.62:8081/publik/kas-warga-rt-04`**

---

## Perintah Manajemen

- **Lihat Log:**
  ```bash
  docker compose logs -f app
  docker compose logs -f webserver
  ```
- **Restart Container:**
  ```bash
  docker compose restart
  ```
- **Hentikan Container:**
  ```bash
  docker compose down
  ```
