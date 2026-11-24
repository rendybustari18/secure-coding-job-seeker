# JobSeeker Portal (Decoupled Vue 3 + PHP REST API)

Aplikasi JobSeeker Portal yang dibangun dengan arsitektur modern memisahkan **Frontend (Vue 3 Single Page Application)** dan **Backend (PHP RESTful API)**, didukung oleh MySQL Database dan phpMyAdmin.

---

## 🏗 Arsitektur Proyek

```
new-lab/
├── docker-compose.yml                 # Orchestration seluruh container
├── README.md                          # Panduan instalasi dan penggunaan
├── backend/                           # PHP 8.2 Backend REST API
│   ├── Dockerfile                     # Dockerfile Apache + PHP 8.2 (pdo_mysql, mysqli, gd)
│   ├── docker-entrypoint.sh           # Auto-migration & Apache runner
│   ├── config/
│   │   ├── env.php                    # Konfigurasi aplikasi & database
│   │   └── database.php               # PDO database connection wrapper
│   ├── database/
│   │   ├── migrate.php                # CLI auto-migration runner
│   │   └── migrations/
│   │       └── 001_initial_schema.sql # Skema & seed data SQL
│   ├── public/
│   │   ├── index.php                  # Entrypoint routing REST API
│   │   └── .htaccess                  # Apache rewrite rules
│   ├── src/
│   │   ├── Controllers/               # Auth, Job, Application, Profile, Analytics
│   │   ├── Middleware/                # CorsMiddleware, AuthMiddleware
│   │   ├── Routes/                    # Router & path dispatcher
│   │   └── Services/                  # JwtService, EmailService, FileUploadService
│   └── uploads/                       # Folder penyimpanan upload (profiles & cvs)
└── frontend/                          # Vue 3 SPA Frontend
    ├── Dockerfile                     # Multi-stage build (Node 20 build -> Nginx serving)
    ├── nginx.conf                     # Nginx config (SPA fallback & reverse proxy /api/)
    ├── package.json                   # Dependencies: Vue 3, Pinia, Vue Router, Axios
    ├── vite.config.js                 # Vite configuration
    ├── index.html                     # HTML Template
    └── src/
        ├── api/                       # Axios API client modules
        ├── components/                # Modular UI components (Navbar, Footer, etc.)
        ├── layouts/                   # Layouts (Default, Member, Company)
        ├── pages/                     # Public, Auth, Member, and Company views
        ├── router/                    # Vue Router dengan navigation guards
        └── store/                     # Pinia state stores (Auth, Jobs)
```

---

## 🚀 Panduan Menjalankan

### 1. Prasyarat

- Docker & Docker Compose (`docker compose` v2 atau `docker-compose` v1)

### 2. Menjalankan Container

Dari direktori `new-lab/`, jalankan perintah:

```bash
docker compose up --build -d
```

> **Catatan Auto-Migration:**  
> Ketika container pertama kali dijalankan, skema database dan seluruh data awal akan **otomatis dimigrasikan** ke MySQL melalui:
>
> 1. Script inisialisasi `/docker-entrypoint-initdb.d/001_initial_schema.sql` pada container MySQL.
> 2. Script `backend/database/migrate.php` yang dipanggil otomatis oleh entrypoint container Backend.

Jika Anda ingin menjalankan migrasi ulang secara manual kapan saja:

```bash
docker compose exec backend php database/migrate.php
```

### 3. Menghentikan Layanan

```bash
docker compose down
```

Jika ingin menghapus seluruh volume database agar bersih:

```bash
docker compose down -v
```

---

## 🌐 Endpoint & Port Mapping

| Layanan              | URL Akses                   | Port Host | Keterangan                       |
| :------------------- | :-------------------------- | :-------- | :------------------------------- |
| **Frontend (Vue 3)** | `http://localhost:5173`     | `5173`    | Web UI Aplikasi Job Seeker       |
| **Public API Docs**  | `http://localhost:5173/docs`| `5173`    | Dokumentasi Interaktif Member, Company, Admin API |
| **Backend REST API** | `http://localhost:8004/api` | `8004`    | Endpoint REST API PHP            |
| **phpMyAdmin**       | `http://localhost:9006`     | `9006`    | Web GUI Manajemen Database MySQL |
| **MySQL Database**   | `localhost:3317`            | `3317`    | Server Database MySQL 8.0        |

---

## 🔑 Kredensial Bawaan

### 1. Database & phpMyAdmin

- **Server / Host:** `mysql` (atau `localhost:3317` dari host)
- **Database:** `db_job_seeker`
- **Username:** `root`
- **Password:** `root`

Akses phpMyAdmin di [http://localhost:9006](http://localhost:9006) dengan:

- **Server:** `mysql`
- **Username:** `root`
- **Password:** `root`

### 2. Akun Pengguna Aplikasi

| Role                    | Username   | Email                  | Password      | Divisi |
| :---------------------- | :--------- | :--------------------- | :------------ | :----- |
| **Member** (Job Seeker) | `member1`  | `member1@example.com`  | `password123` | -      |
| **Company** (Recruiter) | `company1` | `company1@example.com` | `company123`  | -      |
| **Company** (Admin)     | `admin`    | `admin@example.com`    | `admin123`    | -      |
# secure-coding-job-seeker
