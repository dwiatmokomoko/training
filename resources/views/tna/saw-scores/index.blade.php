@extends('layouts.app', [
    'title' => 'Nilai SAW',
    'subtitle' => 'CRUD nilai manual per pegawai, periode, dan kriteria. Nilai ini dipakai sebagai override pada analisis SAW.',
])

@section('content')
    <div class="mb-5 flex flex-col justify-between gap-3 md:flex-row md:items-center">
        @include('partials.period-filter')
        @if (auth()->user()->hasAnyRole(['admin', 'kepegawaian']))
            <a href="{{ route('tna.saw-scores.create', ['period' => $period?->id]) }}" class="btn-primary">Tambah Nilai SAW</a>
        @endif
    </div>

    @if ($employeesWithoutManualScores->isNotEmpty())
        <section class="mb-6 rounded-lg border border-ma-gold/50 bg-ma-gold/10 p-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div>
                    <h3 class="font-bold text-stone-950">Pegawai belum punya nilai manual pada periode ini</h3>
                    <p class="mt-1 text-sm text-stone-600">Sistem tetap bisa memakai nilai otomatis/default, tetapi untuk pegawai baru sebaiknya input nilai SAW agar ranking lebih akurat.</p>
                </div>
                <span class="badge muted">{{ $employeesWithoutManualScores->count() }} ditampilkan</span>
            </div>
            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($employeesWithoutManualScores as $employee)
                    <div class="rounded-md border border-white/70 bg-white p-3">
                        <div class="font-semibold">{{ $employee->name }}</div>
                        <div class="mt-1 text-xs text-stone-500">{{ $employee->position?->name ?? '-' }}</div>
                        @if (auth()->user()->hasAnyRole(['admin', 'kepegawaian']))
                            <a href="{{ route('tna.saw-scores.create', ['period' => $period?->id, 'employee' => $employee->id]) }}" class="mt-3 inline-flex text-xs font-bold text-ma-red">Input nilai</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="panel">
        <div class="panel-header">
            <div>
                <h3>Daftar Nilai SAW Manual</h3>
                <p>{{ $period?->name }} - nilai manual akan menimpa nilai otomatis pada matriks SAW.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table datatable">
                <thead>
                    <tr>
                        <th>Pegawai</th>
                        <th>Periode</th>
                        <th>Kriteria</th>
                        <th>Nilai</th>
                        <th>Penilai</th>
                        <th>Tanggal</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($scores as $score)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $score->employee?->name }}</div>
                                <div class="text-xs text-stone-500">{{ $score->employee?->position?->name }}</div>
                            </td>
                            <td>{{ $score->period?->name }}</td>
                            <td>
                                <div class="font-semibold">{{ $score->criterion?->code }}</div>
                                <div class="text-xs text-stone-500">{{ $score->criterion?->name }}</div>
                            </td>
                            <td><span class="badge">{{ number_format($score->value, 2) }}</span></td>
                            <td>{{ $score->assessor_name ?: '-' }}</td>
                            <td>{{ $score->assessed_at?->format('d/m/Y') ?: '-' }}</td>
                            <td>{{ $score->notes ?: '-' }}</td>
                            <td>
                                @if (auth()->user()->hasAnyRole(['admin', 'kepegawaian']))
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('tna.saw-scores.edit', $score) }}" class="btn-table">Edit</a>
                                        <form method="POST" action="{{ route('tna.saw-scores.destroy', $score) }}" onsubmit="return confirm('Hapus nilai SAW ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="badge muted">Lihat</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
