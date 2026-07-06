@extends('layouts.app', [
    'title' => 'Perencanaan Pelatihan',
    'subtitle' => 'Bentuk rencana tahunan otomatis dari ranking kebutuhan pelatihan.',
])

@section('content')
    <section class="grid gap-6 xl:grid-cols-[.8fr_1.2fr]">
        @if (auth()->user()->hasAnyRole(['admin', 'kepegawaian']))
            <div class="panel">
                <div class="panel-header"><h3>Buat Rencana Otomatis</h3><p>Ambil peserta teratas dari hasil SAW</p></div>
                <form method="POST" action="{{ route('tna.planning.store') }}" class="grid gap-4">
                    @csrf
                    <label class="form-label">Periode
                        <select name="assessment_period_id" class="form-input">
                            @foreach ($periods as $item)
                                <option value="{{ $item->id }}" @selected($period?->id === $item->id)>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="form-label">Nama Rencana
                        <input name="name" class="form-input" value="Rencana Pelatihan Tahunan {{ now()->year }}" required>
                    </label>
                    <label class="form-label">Tahun Anggaran
                        <input name="year" type="number" class="form-input" value="{{ now()->year }}" required>
                    </label>
                    <label class="form-label">Jumlah Peserta Teratas
                        <input name="participant_limit" type="number" class="form-input" value="10" min="1" max="100" required>
                    </label>
                    <label class="form-label">Estimasi Anggaran
                        <input name="estimated_budget" type="number" class="form-input" placeholder="0">
                    </label>
                    <button class="btn-primary w-full">Buat Draft Rencana</button>
                </form>
            </div>
        @else
            <div class="panel">
                <div class="panel-header"><h3>Mode Pimpinan</h3><p>Anda dapat meninjau calon peserta dan draft rencana.</p></div>
                <p class="text-sm text-stone-600">Pembuatan draft rencana dilakukan oleh admin atau petugas kepegawaian, lalu dapat digunakan sebagai bahan persetujuan pimpinan.</p>
            </div>
        @endif

        <div class="panel">
            <div class="panel-header"><h3>Calon Peserta Prioritas</h3><p>{{ $period?->name }}</p></div>
            <div class="overflow-x-auto">
                <table class="data-table datatable">
                    <thead><tr><th>Rank</th><th>Pegawai</th><th>Kelas</th><th>Pelatihan</th></tr></thead>
                    <tbody>
                        @foreach ($rankings as $row)
                            <tr>
                                <td class="font-bold text-ma-red">#{{ $row['rank'] }}</td>
                                <td>{{ $row['employee']->name }}</td>
                                <td><span class="badge">{{ $row['priority'] }}</span></td>
                                <td>{{ $row['training']?->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="panel mt-6">
        <div class="panel-header"><h3>Draft Rencana</h3><p>Daftar rencana yang telah dibuat</p></div>
        <div class="grid gap-4">
            @forelse ($plans as $plan)
                <div class="rounded-md border border-stone-200 p-4">
                    <div class="flex flex-col justify-between gap-2 md:flex-row md:items-center">
                        <div>
                            <h4 class="font-bold">{{ $plan->name }}</h4>
                            <p class="text-sm text-stone-600">{{ $plan->period?->name }} - {{ $plan->participants->count() }} peserta - {{ $plan->status }}</p>
                        </div>
                        <span class="badge muted">Rp {{ number_format($plan->estimated_budget ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <p class="empty">Belum ada draft rencana pelatihan.</p>
            @endforelse
        </div>
    </section>
@endsection
