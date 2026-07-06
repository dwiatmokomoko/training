# Panduan Penggunaan Sistem TNA

## Akses Sistem
1. Jalankan file `run_system.bat` atau `php artisan serve`
2. Buka browser dan akses: http://127.0.0.1:8000
3. Sistem siap digunakan (tidak perlu login untuk demo)

## Menu Utama

### 1. Dashboard
**Fungsi**: Ringkasan sistem dan analisis SAW
- Lihat statistik pegawai dan penilaian
- Klik "Jalankan Analisis SAW" untuk memulai analisis
- Lihat hasil prioritas pelatihan terbaru
- Akses cepat ke fitur lain

### 2. Data Pegawai
**Fungsi**: Manajemen data pegawai
- **Tambah Pegawai**: Input data pegawai baru
- **Edit/Hapus**: Kelola data pegawai existing
- **Detail**: Lihat profil lengkap dan riwayat penilaian

**Data yang diperlukan**:
- NIP (unik)
- Nama lengkap
- Email (unik)
- Jabatan
- Tingkat pendidikan
- Pengalaman kerja (tahun)
- Tanggal lahir
- Jenis kelamin
- Nomor telepon

### 3. Penilaian (Assessment)
**Fungsi**: Input penilaian pegawai berdasarkan kriteria SAW

**Kriteria Penilaian**:
1. **Kompetensi Teknis (30%)** - Nilai 0-100
2. **Kinerja Pegawai (25%)** - Nilai 0-100  
3. **Pengalaman Kerja (20%)** - Nilai 0-100
4. **Tingkat Pendidikan (15%)** - Nilai 0-100
5. **Usia (10%)** - Nilai 0-100

**Cara Input**:
- **Individual**: Satu pegawai, satu kriteria
- **Bulk**: Satu pegawai, semua kriteria sekaligus

### 4. Kebutuhan Pelatihan
**Fungsi**: Hasil analisis SAW dan manajemen pelatihan
- Lihat prioritas pelatihan berdasarkan ranking
- Update status pelatihan (Pending/Disetujui/Ditolak/Selesai)
- Filter berdasarkan status
- Lihat detail rekomendasi pelatihan

### 5. Laporan
**Fungsi**: Export dan analisis data
- Unduh laporan prioritas pelatihan
- Statistik dan trend analysis
- Data untuk keperluan administrasi

## Alur Kerja Sistem

### Langkah 1: Input Data Pegawai
1. Masuk ke menu "Data Pegawai"
2. Klik "Tambah Pegawai"
3. Isi semua data yang diperlukan
4. Simpan data

### Langkah 2: Input Penilaian
1. Masuk ke menu "Penilaian"
2. Pilih "Tambah Penilaian" atau "Penilaian Bulk"
3. Pilih pegawai dan kriteria
4. Berikan nilai 0-100 untuk setiap kriteria
5. Simpan penilaian

### Langkah 3: Jalankan Analisis SAW
1. Kembali ke Dashboard
2. Klik tombol "Jalankan Analisis SAW"
3. Sistem akan menghitung skor dan ranking otomatis
4. Hasil akan muncul di tabel prioritas

### Langkah 4: Review dan Kelola Hasil
1. Masuk ke menu "Kebutuhan Pelatihan"
2. Review prioritas dan rekomendasi
3. Update status pelatihan sesuai keputusan
4. Unduh laporan jika diperlukan

## Tips Penggunaan

### Penilaian yang Efektif
- Pastikan semua pegawai memiliki penilaian lengkap (5 kriteria)
- Gunakan skala yang konsisten (0-100)
- Berikan catatan untuk referensi di masa depan
- Update penilaian secara berkala

### Interpretasi Hasil SAW
- **Skor tinggi** = Prioritas pelatihan tinggi
- **Ranking 1-5** = Prioritas sangat tinggi (warna merah)
- **Ranking 6-10** = Prioritas tinggi (warna kuning)
- **Ranking >10** = Prioritas normal (warna abu-abu)

### Manajemen Status Pelatihan
- **Pending**: Menunggu persetujuan
- **Disetujui**: Pelatihan telah disetujui
- **Ditolak**: Pelatihan ditolak dengan alasan
- **Selesai**: Pelatihan telah selesai dilaksanakan

## Troubleshooting

### Masalah Umum

**Q: Analisis SAW tidak menghasilkan data**
A: Pastikan semua pegawai memiliki penilaian lengkap untuk 5 kriteria

**Q: Tidak bisa input penilaian**
A: Cek apakah kombinasi pegawai+kriteria sudah ada (tidak boleh duplikat)

**Q: Server tidak bisa diakses**
A: Pastikan menjalankan `php artisan serve` dan akses http://127.0.0.1:8000

**Q: Error database**
A: Cek koneksi PostgreSQL dan konfigurasi .env

### Kontak Support
Untuk bantuan teknis, hubungi administrator sistem atau buat issue di repository.

---

**Sistem TNA v1.0**  
Pengadilan Negeri Sleman  
© 2026