# Setup PostgreSQL untuk Sistem TNA

## Instalasi PostgreSQL

### Windows
1. Download PostgreSQL dari https://www.postgresql.org/download/windows/
2. Install dengan pengaturan default
3. Catat password untuk user `postgres`
4. Tambahkan PostgreSQL ke PATH environment variable

### Alternatif dengan Docker
```bash
docker run --name postgres-tna -e POSTGRES_PASSWORD=password -p 5432:5432 -d postgres:15
```

## Konfigurasi Database

1. Buat database baru:
```sql
CREATE DATABASE training_need_analysis 
WITH OWNER = postgres 
ENCODING = 'UTF8';
```

2. Update file `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=training_need_analysis
DB_USERNAME=postgres
DB_PASSWORD=your_password_here
```

3. Jalankan migrasi:
```bash
php artisan migrate --seed
```

## Verifikasi Koneksi

Test koneksi database:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> echo "Database connected successfully!";
```

## Troubleshooting

### Error: database does not exist
- Pastikan database `training_need_analysis` sudah dibuat
- Cek kredensial di file `.env`

### Error: psql command not found
- Pastikan PostgreSQL sudah terinstall
- Tambahkan PostgreSQL bin directory ke PATH

### Error: connection refused
- Pastikan PostgreSQL service berjalan
- Cek port 5432 tidak digunakan aplikasi lain

## Backup dan Restore

### Backup
```bash
pg_dump -U postgres training_need_analysis > backup.sql
```

### Restore
```bash
psql -U postgres training_need_analysis < backup.sql
```