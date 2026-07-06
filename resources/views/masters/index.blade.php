@extends('layouts.app', [
    'title' => 'Master Data',
    'subtitle' => 'Kelola seluruh data referensi sistem TNA SAW.',
])

@section('content')
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($resources as $resource)
            <a href="{{ route('masters.crud.index', $resource['key']) }}" class="panel compact group transition hover:-translate-y-0.5 hover:border-ma-gold hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase text-ma-red">CRUD Master</p>
                        <h3 class="mt-1 text-lg font-bold text-stone-950 group-hover:text-ma-red">{{ $resource['label'] }}</h3>
                    </div>
                    <span class="badge">{{ number_format($resource['model']::query()->count()) }}</span>
                </div>
                <p class="mt-4 text-sm text-stone-600">Tambah, ubah, hapus, cari, dan sortir data {{ strtolower($resource['label']) }}.</p>
            </a>
        @endforeach
    </section>
@endsection
