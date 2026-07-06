@extends('layouts.app', [
    'title' => 'Laporan',
    'subtitle' => 'Laporan TNA per pegawai, rekap gap kompetensi, dan export CSV.',
])

@section('content')
    <div class="mb-5 flex flex-col justify-between gap-3 md:flex-row md:items-center">
        @include('partials.period-filter')
        <a href="{{ route('tna.export', ['period' => $period?->id]) }}" class="btn-primary">Export CSV</a>
    </div>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h3>Laporan Ranking Kebutuhan Pelatihan</h3>
                <p>Format ringkas untuk bahan persetujuan pimpinan.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table datatable">
                <thead><tr><th>Rank</th><th>NIP</th><th>Nama</th><th>Unit</th><th>Skor</th><th>Kelas</th><th>Rekomendasi</th></tr></thead>
                <tbody>
                    @foreach ($rankings as $row)
                        <tr>
                            <td class="font-bold">#{{ $row['rank'] }}</td>
                            <td>{{ $row['employee']->nip }}</td>
                            <td class="font-medium">{{ $row['employee']->name }}</td>
                            <td>{{ $row['employee']->unit?->name }}</td>
                            <td>{{ number_format($row['score'], 4) }}</td>
                            <td><span class="badge">{{ $row['priority'] }}</span></td>
                            <td>{{ $row['training']?->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
