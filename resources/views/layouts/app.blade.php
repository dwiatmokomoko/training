<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TNA SAW PN Sleman' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-stone-950 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="bg-ma-red text-white lg:fixed lg:inset-y-0 lg:w-72">
            <div class="flex items-center gap-3 border-b border-white/10 px-5 py-5">
                <div class="grid h-11 w-11 place-items-center rounded-md border border-ma-gold/50 bg-ma-gold text-ma-red font-black">MA</div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-ma-gold">PN Sleman</p>
                    <h1 class="font-bold leading-tight">Training Need Analysis</h1>
                </div>
            </div>
            <nav class="grid gap-4 p-3 text-sm">
                @php
                    $groups = [
                        'Utama' => [
                            ['dashboard', 'Dashboard'],
                        ],
                        'Data & Nilai' => [
                            ['masters.index', 'Master Data'],
                            ['tna.assessments', 'Penilaian Kinerja'],
                            ['tna.saw-scores.index', 'Nilai SAW'],
                        ],
                        'Analisis' => [
                            ['tna.analysis', 'Analisis SAW'],
                            ['tna.planning', 'Perencanaan'],
                            ['tna.report', 'Laporan'],
                        ],
                        'Bantuan' => [
                            ['help.workflow', 'Alur Penggunaan'],
                        ],
                    ];
                @endphp
                @foreach ($groups as $groupLabel => $items)
                    <div>
                        <p class="mb-2 px-4 text-[11px] font-bold uppercase tracking-wide text-ma-gold/90">{{ $groupLabel }}</p>
                        <div class="grid gap-1">
                            @foreach ($items as [$route, $label])
                                @php
                                    $active = request()->routeIs($route)
                                        || ($route === 'masters.index' && request()->routeIs('masters.*'))
                                        || ($route === 'tna.saw-scores.index' && request()->routeIs('tna.saw-scores.*'));
                                @endphp
                                <a href="{{ route($route) }}" class="rounded-md px-4 py-3 font-medium transition hover:bg-white/10 {{ $active ? 'bg-ma-gold text-ma-red shadow-sm' : 'text-white/85' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>
            <div class="px-5 py-4 text-xs leading-relaxed text-white/70">
                Metode Simple Additive Weighting untuk prioritas kebutuhan pelatihan pegawai berdasarkan kinerja, riwayat pelatihan, masa jabatan, dan urgensi promosi.
            </div>
            @auth
                <div class="mx-3 mb-4 rounded-md border border-white/10 bg-white/5 p-3 text-sm">
                    <div class="font-semibold">{{ auth()->user()->name }}</div>
                    <div class="mt-1 text-xs text-ma-gold">{{ auth()->user()->roleLabel() }}</div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button class="w-full rounded-md border border-white/15 px-3 py-2 text-xs font-bold text-white/85 transition hover:bg-white/10" type="submit">Logout</button>
                    </form>
                </div>
            @endauth
        </aside>

        <main class="min-w-0 flex-1 lg:pl-72">
            <header class="border-b border-stone-200 bg-white">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-5 sm:px-6 lg:px-8">
                    <p class="text-xs font-semibold uppercase tracking-wide text-ma-red">Sistem Pendukung Keputusan</p>
                    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-end">
                        <div>
                            <h2 class="text-2xl font-bold text-stone-950">{{ $title ?? 'Dashboard' }}</h2>
                            <p class="mt-1 max-w-3xl text-sm text-stone-600">{{ $subtitle ?? 'Pengadilan Negeri Sleman' }}</p>
                        </div>
                        @isset($actions)
                            <div class="flex flex-wrap gap-2">{{ $actions }}</div>
                        @endisset
                    </div>
                </div>
            </header>

            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @if (session('status'))
                    <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <strong>Periksa input:</strong> {{ $errors->first() }}
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
