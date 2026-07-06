@extends('layouts.app', [
    'title' => 'Penilaian Kinerja',
    'subtitle' => 'Input dan monitoring nilai indikator kompetensi yang menjadi kriteria C1.',
])

@section('content')
    <div class="mb-5">
        @include('partials.period-filter')
    </div>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h3>Riwayat Nilai Indikator</h3>
                <p>Skala 1 sampai 5; nilai rendah pada C1 berarti kebutuhan pelatihan lebih tinggi.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table datatable">
                <thead><tr><th>Pegawai</th><th>Rumpun</th><th>Indikator</th><th>Nilai</th><th>Catatan</th></tr></thead>
                <tbody>
                    @foreach ($scores as $score)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $score->employee->name }}</div>
                                <div class="text-xs text-stone-500">{{ $score->employee->position?->name }}</div>
                            </td>
                            <td>{{ $score->employee->group?->code }}</td>
                            <td>{{ $score->indicator->name }}</td>
                            <td><span class="badge muted">{{ $score->score }}</span></td>
                            <td>{{ $score->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $scores->links() }}
    </section>
@endsection
