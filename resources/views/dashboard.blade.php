@extends('layouts.app', [
    'title' => 'Dashboard TNA SAW',
    'subtitle' => 'Ringkasan prioritas kebutuhan pelatihan pegawai Pengadilan Negeri Sleman.',
])

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="stat-card">
            <span>Total Pegawai</span>
            <strong>{{ number_format($employeeCount) }}</strong>
            <small>Data pegawai aktif dari Excel</small>
        </div>
        <a href="{{ route('tna.saw-scores.index', ['period' => $period?->id]) }}" class="stat-card block transition hover:border-ma-gold hover:shadow-md">
            <span>Belum Nilai Manual</span>
            <strong>{{ number_format($manualScoreMissingCount) }}</strong>
            <small>Klik untuk input Nilai SAW</small>
        </a>
        <div class="stat-card">
            <span>Prioritas Wajib</span>
            <strong>{{ number_format($mandatoryCount) }}</strong>
            <small>Perlu ditangani lebih awal</small>
        </div>
        <div class="stat-card">
            <span>Prioritas</span>
            <strong>{{ number_format($priorityCount) }}</strong>
            <small>Masuk rencana tahunan</small>
        </div>
        <div class="stat-card">
            <span>Rencana Pelatihan</span>
            <strong>{{ number_format($planCount) }}</strong>
            <small>Draft yang sudah dibuat</small>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[1.25fr_.75fr]">
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3>Ranking Prioritas Pelatihan</h3>
                    <p>Periode: {{ $period?->name ?? 'Belum ada periode' }}</p>
                </div>
                <a href="{{ route('tna.analysis') }}" class="btn-secondary">Lihat Analisis</a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table datatable">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Pegawai</th>
                            <th>Jabatan</th>
                            <th>Skor</th>
                            <th>Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rankings as $row)
                            <tr>
                                <td class="font-bold text-ma-red">#{{ $row['rank'] }}</td>
                                <td>
                                    <div class="font-semibold">{{ $row['employee']->name }}</div>
                                    <div class="text-xs text-stone-500">{{ $row['employee']->nip }}</div>
                                </td>
                                <td>{{ $row['employee']->position?->name ?? '-' }}</td>
                                <td class="font-semibold">{{ number_format($row['score'], 4) }}</td>
                                <td><span class="badge">{{ $row['priority'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty">Belum ada data ranking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3>Komposisi Rumpun</h3>
                    <p>Distribusi pegawai per rumpun jabatan</p>
                </div>
            </div>
            <div class="grid gap-4">
                @foreach ($groupStats as $group)
                    @php $percent = $employeeCount ? round(($group->employees_count / $employeeCount) * 100) : 0; @endphp
                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium">{{ $group->name }}</span>
                            <span class="text-stone-500">{{ $group->employees_count }} pegawai</span>
                        </div>
                        <div class="h-3 rounded-full bg-stone-200">
                            <div class="h-3 rounded-full bg-ma-red" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 rounded-md border border-ma-gold/40 bg-ma-gold/10 p-4 text-sm text-stone-700">
                Rata-rata skor SAW saat ini <strong>{{ number_format($averageScore, 4) }}</strong>. Semakin tinggi skor, semakin tinggi prioritas kebutuhan pelatihan.
            </div>
        </div>
    </section>
@endsection
