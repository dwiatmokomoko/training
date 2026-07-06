@extends('layouts.app', [
    'title' => 'Alur Penggunaan Sistem',
    'subtitle' => 'Panduan singkat operasional TNA SAW dari setup master data sampai laporan.',
])

@section('content')
    <section class="grid gap-4 lg:grid-cols-2">
        @foreach ([
            ['1', 'Siapkan Master Data', 'Isi rumpun, unit kerja, jabatan, pegawai, pelatihan, indikator kinerja, kriteria SAW, dan periode penilaian.'],
            ['2', 'Input Pegawai Baru', 'Tambahkan pegawai melalui Master Data > Pegawai. Lengkapi jabatan, unit, rumpun, TMT jabatan, tanggal lahir, dan status aktif.'],
            ['3', 'Input Nilai', 'Masukkan nilai kinerja di Penilaian Kinerja atau input nilai manual di Nilai SAW, terutama untuk pegawai baru yang belum punya riwayat.'],
            ['4', 'Cek Analisis SAW', 'Buka Analisis SAW, pilih periode, lalu tampilkan atau sembunyikan detail perhitungan sesuai kebutuhan.'],
            ['5', 'Buat Rencana Pelatihan', 'Gunakan ranking SAW untuk membuat draft rencana pelatihan otomatis dan tentukan jumlah peserta prioritas.'],
            ['6', 'Cetak Laporan', 'Buka Laporan untuk rekap ranking, rekomendasi pelatihan, dan export CSV sebagai bahan pimpinan.'],
        ] as [$number, $title, $description])
            <div class="panel compact">
                <div class="flex gap-4">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-ma-gold font-black text-ma-red">{{ $number }}</div>
                    <div>
                        <h3 class="font-bold text-stone-950">{{ $title }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-stone-600">{{ $description }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <section class="panel mt-6">
        <div class="panel-header">
            <div>
                <h3>Alur Pegawai Baru</h3>
                <p>Kasus khusus ketika pegawai belum punya nilai dan riwayat pelatihan.</p>
            </div>
        </div>
        <ol class="grid gap-3 text-sm text-stone-700 md:grid-cols-2">
            <li class="rounded-md border border-stone-200 p-4"><strong>1. Tambah pegawai</strong><br>Masuk Master Data > Pegawai, isi profil dasar dan status aktif.</li>
            <li class="rounded-md border border-stone-200 p-4"><strong>2. Input nilai manual</strong><br>Masuk Nilai SAW, pilih periode dan pegawai, lalu isi nilai C1-C5 atau kriteria aktif lainnya.</li>
            <li class="rounded-md border border-stone-200 p-4"><strong>3. Review ranking</strong><br>Buka Analisis SAW. Pegawai baru langsung masuk matriks ranking karena nilai manual tersedia.</li>
            <li class="rounded-md border border-stone-200 p-4"><strong>4. Lengkapi riwayat</strong><br>Jika data riwayat jabatan/pelatihan sudah ada, input di Master Data agar nilai otomatis semakin akurat.</li>
        </ol>
    </section>
@endsection
