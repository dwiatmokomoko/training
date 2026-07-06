@extends('layouts.app', [
    'title' => 'Detail Pegawai',
    'subtitle' => 'Profil, riwayat jabatan, riwayat pelatihan, dan nilai pegawai.',
])

@section('content')
    <div class="mb-5 flex flex-wrap gap-2">
        <a href="{{ route('masters.crud.index', 'pegawai') }}" class="btn-secondary">Kembali</a>
        @if (auth()->user()->hasAnyRole(['admin', 'kepegawaian']))
            <a href="{{ route('masters.crud.edit', ['pegawai', $employee->id]) }}" class="btn-primary">Edit Pegawai</a>
            <a href="{{ route('masters.crud.create', 'riwayat-pelatihan') }}" class="btn-secondary">Tambah Riwayat Pelatihan</a>
        @endif
    </div>

    <section class="grid gap-6 xl:grid-cols-[.8fr_1.2fr]">
        <aside class="panel">
            <div class="panel-header">
                <div>
                    <h3>{{ $employee->name }}</h3>
                    <p>{{ $employee->nip }}</p>
                </div>
                <span class="badge muted">{{ $employee->status }}</span>
            </div>
            <dl class="grid gap-3 text-sm">
                @foreach ([
                    'Jabatan' => $employee->position?->name,
                    'Rumpun' => $employee->group?->name,
                    'Unit Kerja' => $employee->unit?->name,
                    'Golongan' => $employee->rank_class,
                    'Jenis Kelamin' => $employee->gender,
                    'Tanggal Lahir' => $employee->birth_date?->format('d/m/Y'),
                    'TMT Jabatan' => $employee->position_started_at?->format('d/m/Y'),
                    'Email' => $employee->email,
                    'No HP' => $employee->phone,
                ] as $label => $value)
                    <div class="rounded-md border border-stone-200 p-3">
                        <dt class="text-xs font-bold uppercase text-stone-500">{{ $label }}</dt>
                        <dd class="mt-1 font-medium text-stone-900">{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>
        </aside>

        <div class="grid gap-6">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h3>Riwayat Pelatihan</h3>
                        <p>Data ini memengaruhi kriteria riwayat pelatihan pada analisis SAW.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table datatable">
                        <thead><tr><th>Pelatihan</th><th>Mulai</th><th>Selesai</th><th>Hasil</th><th>Sertifikat</th></tr></thead>
                        <tbody>
                            @foreach ($employee->trainingHistories as $history)
                                <tr>
                                    <td>{{ $history->training?->name ?? '-' }}</td>
                                    <td>{{ $history->started_at?->format('d/m/Y') ?: '-' }}</td>
                                    <td>{{ $history->ended_at?->format('d/m/Y') ?: '-' }}</td>
                                    <td>{{ $history->result ?: '-' }}</td>
                                    <td>{{ $history->certificate_number ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header"><h3>Riwayat Jabatan</h3><p>Perubahan jabatan dan masa jabatan pegawai.</p></div>
                <div class="overflow-x-auto">
                    <table class="data-table datatable">
                        <thead><tr><th>Jabatan</th><th>Mulai</th><th>Selesai</th><th>Jenis</th><th>Keterangan</th></tr></thead>
                        <tbody>
                            @foreach ($employee->positionHistories as $history)
                                <tr>
                                    <td>{{ $history->position?->name ?? '-' }}</td>
                                    <td>{{ $history->started_at?->format('d/m/Y') ?: '-' }}</td>
                                    <td>{{ $history->ended_at?->format('d/m/Y') ?: '-' }}</td>
                                    <td>{{ $history->history_type ?: '-' }}</td>
                                    <td>{{ $history->notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="panel">
                    <div class="panel-header"><h3>Nilai Kinerja</h3><p>Input indikator kompetensi.</p></div>
                    <div class="grid gap-3 text-sm">
                        @forelse ($employee->performanceScores as $score)
                            <div class="rounded-md border border-stone-200 p-3">
                                <div class="font-semibold">{{ $score->indicator?->name }}</div>
                                <div class="text-stone-600">Nilai {{ $score->score }} - {{ $score->notes ?: 'Tanpa catatan' }}</div>
                            </div>
                        @empty
                            <p class="empty">Belum ada nilai kinerja.</p>
                        @endforelse
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-header"><h3>Nilai SAW Manual</h3><p>Override nilai otomatis per kriteria.</p></div>
                    <div class="grid gap-3 text-sm">
                        @forelse ($employee->sawScores as $score)
                            <div class="rounded-md border border-stone-200 p-3">
                                <div class="font-semibold">{{ $score->criterion?->code }} - {{ $score->criterion?->name }}</div>
                                <div class="text-stone-600">{{ $score->period?->name }}: nilai {{ number_format($score->value, 2) }}</div>
                            </div>
                        @empty
                            <p class="empty">Belum ada nilai SAW manual.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </section>
@endsection
