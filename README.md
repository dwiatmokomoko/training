# TNA SAW PN Sleman

Aplikasi pendukung keputusan kebutuhan pelatihan pegawai Pengadilan Negeri Sleman berbasis Laravel 12, PostgreSQL, Tailwind CSS, dan metode Simple Additive Weighting (SAW).

## Fitur

- Dashboard ringkasan prioritas pelatihan, komposisi rumpun pegawai, dan status rencana.
- CRUD master data rumpun, unit kerja, jabatan, pegawai, pelatihan, indikator kinerja, kriteria SAW, periode, riwayat jabatan, dan riwayat pelatihan.
- Detail pegawai dengan profil, riwayat jabatan, riwayat pelatihan, nilai kinerja, dan nilai SAW manual.
- Tabel interaktif menggunakan DataTables untuk pencarian, sortir, paging, dan tampilan data yang lebih rapi.
- Import seed dari file Excel:
  - `database/imports/data_pegawai_pn.xlsx`
  - `database/imports/template_import_database_tna_saw_pn_sleman.xlsx`
- Penilaian kinerja berbasis indikator kompetensi.
- CRUD Nilai SAW manual per periode, pegawai, dan kriteria untuk pegawai baru atau koreksi nilai otomatis.
- Analisis TNA dengan normalisasi benefit/cost dan bobot SAW.
- Detail perhitungan SAW dapat ditampilkan/disembunyikan di halaman analisis.
- Hasil analisis dipisah per rumpun pegawai: Hakim, Kepaniteraan, dan Kesekretariatan.
- Rekomendasi jenis pelatihan per pegawai.
- Pembuatan draft rencana pelatihan otomatis dari ranking SAW.
- Laporan ranking TNA dan export CSV.
- Tema hijau dan emas.
- Halaman Alur Penggunaan untuk memandu operator dari master data sampai laporan.
- Multi-user role: admin, petugas kepegawaian, dan pimpinan/ketua.

## Role dan Permission

| Role | Akses Utama |
| --- | --- |
| Admin | Akses penuh ke seluruh master data, nilai, analisis, perencanaan, dan laporan. |
| Petugas Kepegawaian | Mengelola pegawai, riwayat jabatan, riwayat pelatihan, penilaian kinerja, nilai SAW, dan draft rencana pelatihan. |
| Pimpinan/Ketua | Melihat dashboard, master data, nilai, analisis, perencanaan, laporan, dan alur penggunaan. Tidak dapat membuka halaman input/edit. |

Akun demo:

- `admin@pn-sleman.go.id` / `password`
- `kepegawaian@pn-sleman.go.id` / `password`
- `pimpinan@pn-sleman.go.id` / `password`

## Kriteria SAW Default

Kriteria diambil dari template import:

| Kode | Kriteria | Atribut | Bobot |
| --- | --- | --- | --- |
| C1 | Penilaian Capaian Kinerja Berbasis Kompetensi | Cost | 0.333 |
| C2 | Riwayat Pelatihan | Benefit | 0.267 |
| C3 | Masa Jabatan Saat Ini | Benefit | 0.200 |
| C4 | Jenjang Jabatan / Riwayat Promosi | Benefit | 0.133 |
| C5 | Usia | Cost | 0.067 |

## Alur Pegawai Baru

1. Tambahkan pegawai di `Master Data > Pegawai`.
2. Lengkapi jabatan, unit kerja, rumpun, TMT jabatan, tanggal lahir, dan status aktif.
3. Masuk ke `Nilai SAW`, pilih periode dan pegawai baru.
4. Input nilai untuk kriteria aktif, misalnya C1 sampai C5.
5. Buka `Analisis SAW`; pegawai baru langsung ikut ranking. Jika detail dibutuhkan, klik `Tampilkan Detail`.

Nilai manual pada `Nilai SAW` menjadi prioritas. Jika nilai manual tidak ada, sistem memakai nilai otomatis dari data kinerja, riwayat pelatihan, masa jabatan, promosi, usia, atau nilai default netral.

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Atur koneksi PostgreSQL di `.env`:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=penilaian_pn_sleman
DB_USERNAME=postgres
DB_PASSWORD=
```

Buat database PostgreSQL:

```sql
CREATE DATABASE penilaian_pn_sleman;
```

Jalankan migration dan seeder:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Login memakai akun demo pada bagian Role dan Permission.

## Verifikasi Lokal

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

Untuk QA cepat tanpa PostgreSQL, bisa memakai SQLite:

```powershell
$env:DB_CONNECTION="sqlite"
$env:DB_DATABASE="D:\web\penilaian_pn_sleman\database\database.sqlite"
php artisan migrate:fresh --seed
php artisan test
```
