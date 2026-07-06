@extends('layouts.app', [
    'title' => 'Analisis Kebutuhan Pelatihan',
    'subtitle' => 'Perhitungan SAW: normalisasi kriteria benefit/cost, pembobotan, dan ranking prioritas.',
])

@section('content')
    <div class="mb-5 flex flex-col justify-between gap-3 md:flex-row md:items-center">
        @include('partials.period-filter')
        <div class="flex flex-wrap gap-2">
            @if (auth()->user()->hasAnyRole(['admin', 'kepegawaian']))
                <a href="{{ route('tna.saw-scores.index', ['period' => $period?->id]) }}" class="btn-primary">Input Nilai SAW</a>
            @endif
            <a href="{{ route('tna.analysis', ['period' => $period?->id, 'detail' => $showDetails ? 0 : 1]) }}" class="btn-secondary">
                {{ $showDetails ? 'Sembunyikan Detail' : 'Tampilkan Detail' }}
            </a>
            <a href="{{ route('tna.export', ['period' => $period?->id]) }}" class="btn-secondary">Export CSV</a>
        </div>
    </div>

    @php
        $rankingsByGroup = $rankings->groupBy(fn ($row) => $row['employee']->group?->name ?? 'Tanpa Rumpun');
    @endphp

    <div class="grid gap-6">
        @foreach ($rankingsByGroup as $groupName => $groupRankings)
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h3>{{ $groupName }}</h3>
                        <p>{{ $period?->name }} - {{ $groupRankings->count() }} pegawai. Detail kriteria {{ $showDetails ? 'ditampilkan' : 'disembunyikan' }}.</p>
                    </div>
                    <span class="badge muted">Tabel {{ $loop->iteration }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table datatable">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Pegawai</th>
                                @if ($showDetails)
                                    @foreach ($criteria as $criterion)
                                        <th>{{ $criterion->code }}</th>
                                    @endforeach
                                @endif
                                <th>Skor Akhir</th>
                                <th>Kelas</th>
                                <th>Rekomendasi</th>
                                <th>Status Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupRankings as $row)
                                <tr>
                                    <td class="font-bold text-ma-red">#{{ $row['rank'] }}</td>
                                    <td>
                                        <div class="font-semibold">{{ $row['employee']->name }}</div>
                                        <div class="text-xs text-stone-500">{{ $row['employee']->position?->name }} - {{ $row['employee']->group?->code }}</div>
                                    </td>
                                    @if ($showDetails)
                                        @foreach ($criteria as $criterion)
                                            <td>
                                                <div class="font-medium">{{ $row['raw'][$criterion->code] ?? '-' }}</div>
                                                <div class="text-xs text-stone-500">N {{ $row['normalized'][$criterion->code] ?? '-' }}</div>
                                                <div class="text-[11px] font-semibold text-ma-red">{{ $row['sources'][$criterion->code] ?? 'Default' }}</div>
                                            </td>
                                        @endforeach
                                    @endif
                                    <td class="font-bold">{{ number_format($row['score'], 4) }}</td>
                                    <td><span class="badge">{{ $row['priority'] }}</span></td>
                                    <td>{{ $row['training']?->name ?? '-' }}</td>
                                    <td>
                                        @if (empty($row['missing']))
                                            <span class="badge muted">Lengkap</span>
                                        @else
                                            <span class="badge">Perlu input</span>
                                            <div class="mt-1 text-xs text-stone-500">{{ implode(', ', $row['missing']) }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach
    </div>

    <details class="panel mt-6" {{ $showDetails ? 'open' : '' }}>
        <summary class="cursor-pointer font-bold text-stone-950">Tahapan Perhitungan SAW</summary>
        <div class="mt-4 grid gap-4 text-sm text-stone-700 lg:grid-cols-3">
            <div class="rounded-md border border-stone-200 p-4">
                <h4 class="font-bold">1. Matriks Keputusan</h4>
                <p class="mt-1">Nilai X diambil dari Nilai SAW manual. Jika belum ada, sistem memakai nilai otomatis dari kinerja, riwayat pelatihan, masa jabatan, promosi, usia, atau nilai default netral.</p>
            </div>
            <div class="rounded-md border border-stone-200 p-4">
                <h4 class="font-bold">2. Normalisasi</h4>
                <p class="mt-1">Benefit: R = X / Max(X). Cost: R = Min(X) / X. Atribut diambil dari master Kriteria SAW.</p>
            </div>
            <div class="rounded-md border border-stone-200 p-4">
                <h4 class="font-bold">3. Nilai Akhir</h4>
                <p class="mt-1">V = SUM(Wj x Rij). W adalah bobot kriteria. Ranking diurutkan dari nilai V terbesar.</p>
            </div>
        </div>
    </details>
@endsection
