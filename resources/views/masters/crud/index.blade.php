@extends('layouts.app', [
    'title' => $config['label'],
    'subtitle' => 'CRUD master data ' . strtolower($config['label']) . ' dengan tabel interaktif.',
])

@section('content')
    <div class="mb-5 flex flex-col justify-between gap-3 md:flex-row md:items-center">
        <a href="{{ route('masters.index') }}" class="btn-secondary">Kembali</a>
        @if (auth()->user()->hasAnyRole(['admin', 'kepegawaian']))
            <a href="{{ route('masters.crud.create', $resource) }}" class="btn-primary">Tambah {{ $config['singular'] }}</a>
        @endif
    </div>

    <section class="panel">
        <div class="panel-header">
            <div>
                <h3>Daftar {{ $config['label'] }}</h3>
                <p>{{ $records->count() }} data tersedia. Gunakan pencarian dan sortir pada tabel.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table datatable">
                <thead>
                    <tr>
                        @foreach ($config['columns'] as $label)
                            <th>{{ $label }}</th>
                        @endforeach
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            @foreach ($config['columns'] as $key => $label)
                                @php
                                    $cell = data_get($record, $key);
                                    if (is_bool($cell)) {
                                        $cell = $cell ? 'Aktif' : 'Tidak Aktif';
                                    } elseif ($cell instanceof \Carbon\CarbonInterface) {
                                        $cell = $cell->format('d/m/Y');
                                    }
                                @endphp
                                <td>{{ $cell ?? '-' }}</td>
                            @endforeach
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    @if ($resource === 'pegawai')
                                        <a href="{{ route('masters.crud.show', [$resource, $record->id]) }}" class="btn-secondary !px-3 !py-1.5 !text-xs">Detail</a>
                                    @endif
                                    @if (auth()->user()->hasAnyRole(['admin', 'kepegawaian']))
                                        <a href="{{ route('masters.crud.edit', [$resource, $record->id]) }}" class="btn-table">Edit</a>
                                        <form method="POST" action="{{ route('masters.crud.destroy', [$resource, $record->id]) }}" onsubmit="return confirm('Hapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-danger" type="submit">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
