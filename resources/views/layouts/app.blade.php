<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Analisa Kebutuhan Pelatihan - Mahkamah Agung')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        :root {
            --ma-green: #157347;
            --ma-dark-green: #0f5132;
            --ma-light-green: #eaf6ef;
            --ma-yellow: #d9a441;
            --ma-dark-yellow: #b78322;
            --ma-light-yellow: #fbf4df;
            --surface: #ffffff;
            --surface-muted: #f6f8fa;
            --line: #e2e8f0;
            --text-main: #182230;
            --text-muted: #667085;
            --shadow-sm: 0 2px 10px rgba(15, 23, 42, 0.06);
            --shadow-md: 0 12px 30px rgba(15, 23, 42, 0.09);
        }

        body {
            color: var(--text-main);
            background: var(--surface-muted);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        
        .app-shell {
            display: flex;
            min-height: 100vh;
            background: var(--surface-muted);
        }

        .sidebar-shell {
            width: 280px;
            flex: 0 0 280px;
            transition: margin-left 0.25s ease, transform 0.25s ease;
            z-index: 1040;
        }

        .content-shell {
            flex: 1;
            min-width: 0;
        }

        body.sidebar-collapsed .sidebar-shell {
            margin-left: -280px;
        }

        .sidebar {
            min-height: 100vh;
            background: #0b2f21;
            box-shadow: 2px 0 18px rgba(15, 23, 42, 0.16);
            position: sticky;
            top: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .sidebar-close {
            display: none;
            width: 36px;
            height: 36px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.08);
            color: white;
            border-radius: 8px;
        }
        
        .sidebar-nav {
            display: grid;
            gap: 0.5rem;
        }

        .sidebar-section-label {
            color: rgba(255,255,255,0.48);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            margin: 0.9rem 0 0.25rem;
            text-transform: uppercase;
        }

        .sidebar .nav-link,
        .sidebar-group-title {
            width: 100%;
            color: rgba(255,255,255,0.84);
            padding: 0.68rem 0.78rem;
            border-radius: 8px;
            transition: all 0.18s ease;
            border: 1px solid transparent;
            background: transparent;
            font-weight: 650;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.62rem;
            text-decoration: none;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active,
        .sidebar-group-title.active {
            color: white;
            background-color: rgba(255,255,255,0.10);
            border-color: rgba(255,255,255,0.10);
        }

        .sidebar .nav-link.active,
        .sidebar-group-title.active {
            background: rgba(255,255,255,0.14);
            box-shadow: inset 3px 0 0 var(--ma-yellow);
        }

        .sidebar .nav-link i,
        .sidebar-group-title i:first-child {
            color: #d9c38b;
            width: 20px;
            text-align: center;
        }

        .sidebar-submenu {
            display: grid;
            gap: 0.2rem;
            margin: 0.3rem 0 0.45rem 0;
            padding-left: 0.55rem;
            border-left: 1px solid rgba(255,255,255,0.12);
        }

        .sidebar-submenu .nav-link {
            font-size: 0.92rem;
            padding: 0.54rem 0.65rem;
            color: rgba(255,255,255,0.74);
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.10);
        }

        .sidebar-user {
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 8px;
            padding: 0.75rem;
            color: rgba(255,255,255,0.82);
            background: rgba(255,255,255,0.06);
            margin-bottom: 0.75rem;
        }

        .logout-button {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: 1px solid rgba(255,255,255,0.16);
            color: white;
            background: rgba(255,255,255,0.08);
            border-radius: 8px;
            padding: 0.65rem 0.75rem;
            font-weight: 700;
        }

        .logout-button:hover {
            background: rgba(255,255,255,0.14);
        }
        
        .main-content {
            background: linear-gradient(180deg, #f8fafc 0%, #f2f6f4 100%);
            min-height: 100vh;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
        }
        
        .card:hover {
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            background: #ffffff;
            color: var(--text-main);
            border-radius: 8px 8px 0 0 !important;
            border-bottom: 1px solid var(--line);
            padding: 0.95rem 1rem;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-primary {
            background: var(--ma-green);
            border: 1px solid var(--ma-green);
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--ma-dark-green);
            border-color: var(--ma-dark-green);
            box-shadow: 0 6px 14px rgba(21, 115, 71, 0.18);
        }
        
        .btn-success {
            background: #f4c765;
            border: 1px solid #f4c765;
            color: #2c2109;
            font-weight: 600;
        }
        
        .btn-success:hover {
            background: #e5b34d;
            border-color: #e5b34d;
            color: #2c2109;
        }
        
        .badge.bg-primary {
            background: var(--ma-green) !important;
        }
        
        .badge.bg-success {
            background: var(--ma-yellow) !important;
            color: #333 !important;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, var(--ma-green) 0%, var(--ma-yellow) 100%);
        }
        
        .text-primary {
            color: var(--ma-green) !important;
        }
        
        .text-success {
            color: var(--ma-dark-green) !important;
        }
        
        .bg-primary {
            background: var(--ma-green) !important;
        }
        
        .bg-success {
            background: var(--ma-yellow) !important;
            color: #333 !important;
        }
        
        .logo-ma {
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .logo-ma img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.24));
        }
        
        .header-gradient {
            background: #ffffff;
            padding: 1.25rem 0;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-radius: 0 0 14px 14px;
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--line);
            color: var(--text-main);
        }

        .sidebar-toggle {
            width: 42px;
            height: 42px;
            border: 1px solid var(--line);
            background: white;
            color: var(--ma-dark-green);
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .sidebar-toggle:hover {
            background: var(--ma-light-green);
            color: var(--ma-dark-green);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.42);
            z-index: 1030;
        }

        .toolbar-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 1rem;
            box-shadow: var(--shadow-sm);
        }

        .toolbar-title {
            margin: 0;
            font-weight: 700;
            color: var(--text-main);
        }

        .toolbar-subtitle {
            margin: 0.25rem 0 0;
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .toolbar-search {
            min-width: min(460px, 100%);
        }

        .form-control,
        .form-select,
        .input-group-text {
            border-color: var(--line);
            border-radius: 8px;
        }

        .input-group > :not(:first-child) {
            margin-left: -1px;
        }

        .table {
            vertical-align: middle;
        }

        .table thead th {
            color: var(--text-muted);
            font-size: 0.82rem;
            letter-spacing: 0;
            text-transform: uppercase;
            border-bottom: 1px solid var(--line);
        }

        .table tbody td {
            border-color: #eef2f3;
        }

        .module-hero {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            background: #ffffff;
            border: 1px solid var(--line);
            border-left: 5px solid var(--ma-green);
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
        }

        .module-hero h3,
        .section-title {
            margin: 0;
            font-weight: 800;
            color: var(--text-main);
        }

        .module-hero p,
        .section-subtitle {
            margin: 0.45rem 0 0;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .module-hero-icon {
            width: 68px;
            height: 68px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: var(--ma-dark-green);
            background: var(--ma-light-green);
            font-size: 1.6rem;
            flex: 0 0 auto;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .module-grid.two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .module-card {
            background: white;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 1rem;
            box-shadow: var(--shadow-sm);
            height: 100%;
        }

        .module-card-head {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .module-card-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: var(--ma-dark-green);
            background: var(--ma-light-yellow);
            flex: 0 0 auto;
        }

        .module-card h6 {
            margin: 0;
            font-weight: 800;
        }

        .module-card p {
            margin: 0.25rem 0 0;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .feature-list {
            display: grid;
            gap: 0.55rem;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .feature-list li {
            display: flex;
            gap: 0.55rem;
            align-items: flex-start;
            color: var(--text-main);
        }

        .feature-list i {
            margin-top: 0.15rem;
            color: var(--ma-green);
        }

        .soft-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .soft-table th,
        .soft-table td {
            padding: 0.85rem;
            border-bottom: 1px solid #edf1f2;
            vertical-align: top;
        }

        .soft-table th {
            color: var(--text-muted);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0;
            background: #f8faf9;
        }

        .data-table-shell {
            background: white;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            padding: 0.75rem;
        }

        .js-data-table {
            width: 100% !important;
        }

        .dt-container {
            color: var(--text-main);
            font-size: 0.94rem;
        }

        .dt-container .dt-layout-row {
            align-items: center;
            margin: 0.2rem 0 0.9rem;
            gap: 0.75rem;
        }

        .dt-container .dt-search input,
        .dt-container .dt-length select {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 0.45rem 0.7rem;
            outline: none;
            background: #fff;
        }

        .dt-container .dt-search input:focus,
        .dt-container .dt-length select:focus {
            border-color: var(--ma-green);
            box-shadow: 0 0 0 3px rgba(31, 122, 58, 0.12);
        }

        .dt-container .dt-paging .dt-paging-button {
            border: 1px solid var(--line) !important;
            border-radius: 8px !important;
            padding: 0.35rem 0.65rem !important;
            margin: 0 0.12rem;
            color: var(--text-main) !important;
            background: white !important;
        }

        .dt-container .dt-paging .dt-paging-button.current,
        .dt-container .dt-paging .dt-paging-button:hover {
            background: var(--ma-green) !important;
            color: white !important;
            border-color: var(--ma-green) !important;
        }

        .dt-container .dt-info {
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .dt-container table.dataTable > thead > tr > th,
        .dt-container table.dataTable > thead > tr > td {
            border-bottom: 1px solid var(--line);
        }

        .dt-container table.dataTable > tbody > tr:hover > * {
            box-shadow: inset 0 0 0 9999px rgba(21, 115, 71, 0.035) !important;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.28rem 0.58rem;
            border-radius: 999px;
            background: var(--ma-light-green);
            color: var(--ma-dark-green);
            font-size: 0.84rem;
            font-weight: 700;
            white-space: nowrap;
        }
        
        .stats-card {
            background: white;
            border-left: 4px solid var(--ma-green);
            transition: all 0.3s;
        }
        
        .stats-card:hover {
            border-left-color: var(--ma-yellow);
            transform: translateY(-3px);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(34,139,34,0.05);
        }
        
        .alert-success {
            background-color: rgba(255,215,0,0.1);
            border-color: var(--ma-yellow);
            color: var(--ma-dark-green);
        }
        
        .alert-danger {
            background-color: rgba(220,53,69,0.1);
            border-color: #dc3545;
            color: #721c24;
        }

        @media (max-width: 991.98px) {
            .app-shell {
                display: block;
            }

            .sidebar-shell {
                position: fixed;
                inset: 0 auto 0 0;
                transform: translateX(-100%);
                margin-left: 0 !important;
            }

            body.sidebar-mobile-open .sidebar-shell {
                transform: translateX(0);
            }

            body.sidebar-mobile-open .sidebar-overlay {
                display: block;
            }

            .sidebar {
                min-height: auto;
                position: relative;
                height: 100vh;
            }

            .sidebar-close {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .toolbar-panel,
            .toolbar-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .toolbar-actions,
            .toolbar-search {
                width: 100%;
            }

            .module-hero {
                flex-direction: column;
            }

            .module-grid,
            .module-grid.two {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            .sidebar,
            .sidebar-shell,
            .sidebar-overlay,
            .header-gradient,
            .toolbar-panel,
            .btn,
            .action-cell,
            .card-actions,
            .modal,
            .pagination-container,
            .search-box,
            .modern-select,
            .refresh-btn,
            .export-btn,
            .print-btn {
                display: none !important;
            }

            .col-md-9,
            .col-lg-10 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }

            .main-content {
                padding: 0 !important;
                background: white !important;
            }

            .card,
            .modern-table {
                box-shadow: none !important;
                border: 1px solid #d7dde0 !important;
            }

            .card:hover,
            .table-row:hover {
                transform: none !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-900 antialiased">
    @php
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $employeeMenuActive = request()->routeIs('employees.*', 'performance', 'assessments.*');
        $trainingMenuActive = request()->routeIs('training-needs.*', 'training-recommendations', 'training-plans');
        $settingsMenuActive = request()->routeIs('users-management', 'positions-competencies', 'master-data', 'system-flow');
    @endphp
    <div class="app-shell min-h-screen">
        <div class="sidebar-overlay" data-sidebar-close></div>
        <aside class="sidebar-shell">
            <div class="sidebar p-3">
                <div class="sidebar-brand mb-4">
                    <div class="d-flex align-items-center">
                        <div class="logo-ma me-2">
                            <img src="{{ asset('storage/logo%20pn.png') }}" alt="Logo Pengadilan Negeri">
                        </div>
                        <div>
                            <h6 class="text-white mb-0">TNA System</h6>
                            <small class="text-white-50">Mahkamah Agung</small>
                        </div>
                    </div>
                    <button type="button" class="sidebar-close" data-sidebar-close aria-label="Tutup menu">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <nav class="sidebar-nav">
                    @if(\App\Support\Access::allows('dashboard.view'))
                    <div class="sidebar-section-label">Utama</div>
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                    @endif

                    @if(\App\Support\Access::allows('employees.view') || \App\Support\Access::allows('employees.manage') || \App\Support\Access::allows('assessments.manage'))
                    <div class="sidebar-section-label">Data</div>
                    <div class="sidebar-group-title {{ $employeeMenuActive ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        Kepegawaian
                    </div>
                    <div class="sidebar-submenu">
                        @if(\App\Support\Access::allows('employees.view') || \App\Support\Access::allows('employees.manage'))
                        <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                            <i class="fas fa-id-card"></i>
                            Data Pegawai
                        </a>
                        @endif
                        @if(\App\Support\Access::allows('assessments.manage'))
                        <a class="nav-link {{ request()->routeIs('performance', 'assessments.*') ? 'active' : '' }}" href="{{ route('performance') }}">
                            <i class="fas fa-clipboard-check"></i>
                            Penilaian Kinerja
                        </a>
                        @endif
                    </div>
                    @endif

                    @if(\App\Support\Access::allows('training-needs.view') || \App\Support\Access::allows('training-needs.manage'))
                    <div class="sidebar-section-label">Proses</div>
                    <div class="sidebar-group-title {{ $trainingMenuActive ? 'active' : '' }}">
                        <i class="fas fa-magnifying-glass-chart"></i>
                        TNA & Pelatihan
                    </div>
                    <div class="sidebar-submenu">
                        <a class="nav-link {{ request()->routeIs('training-needs.index', 'training-needs.show') ? 'active' : '' }}" href="{{ route('training-needs.index') }}">
                            <i class="fas fa-chart-line"></i>
                            Analisis TNA
                        </a>
                        <a class="nav-link {{ request()->routeIs('training-recommendations') ? 'active' : '' }}" href="{{ route('training-recommendations') }}">
                            <i class="fas fa-graduation-cap"></i>
                            Rekomendasi
                        </a>
                        @if(\App\Support\Access::allows('training-needs.manage'))
                        <a class="nav-link {{ request()->routeIs('training-plans') ? 'active' : '' }}" href="{{ route('training-plans') }}">
                            <i class="fas fa-calendar-check"></i>
                            Perencanaan
                        </a>
                        @endif
                        @if(\App\Support\Access::allows('reports.view'))
                        <a class="nav-link {{ request()->routeIs('training-needs.report') ? 'active' : '' }}" href="{{ route('training-needs.report') }}">
                            <i class="fas fa-chart-bar"></i>
                            Laporan
                        </a>
                        @endif
                    </div>
                    @endif

                    <div class="sidebar-section-label">Sistem</div>
                    <div class="sidebar-group-title {{ $settingsMenuActive ? 'active' : '' }}">
                        <i class="fas fa-sliders"></i>
                        Pengaturan
                    </div>
                    <div class="sidebar-submenu">
                        @if(\App\Support\Access::allows('master-data.manage'))
                        <a class="nav-link {{ request()->routeIs('users-management') ? 'active' : '' }}" href="{{ route('users-management') }}">
                            <i class="fas fa-user-shield"></i>
                            Pengguna & Role
                        </a>
                        <a class="nav-link {{ request()->routeIs('positions-competencies') ? 'active' : '' }}" href="{{ route('positions-competencies') }}">
                            <i class="fas fa-sitemap"></i>
                            Jabatan & Kompetensi
                        </a>
                        <a class="nav-link {{ request()->routeIs('master-data') ? 'active' : '' }}" href="{{ route('master-data') }}">
                            <i class="fas fa-database"></i>
                            Master Data
                        </a>
                        @endif
                        <a class="nav-link {{ request()->routeIs('system-flow') ? 'active' : '' }}" href="{{ route('system-flow') }}">
                            <i class="fas fa-route"></i>
                            Alur Sistem
                        </a>
                    </div>
                </nav>

                <div class="sidebar-footer">
                    <div class="sidebar-user">
                        <div class="fw-bold">{{ $currentUser?->name ?? 'Admin Demo' }}</div>
                        <small>{{ $currentUser?->role_label ?? 'Mode tanpa login' }}</small>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-button">
                            <i class="fas fa-right-from-bracket"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="content-shell">
            <div class="main-content min-h-screen p-4 lg:p-6">
                <!-- Header -->
                <div class="header-gradient">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Tampilkan atau sembunyikan sidebar">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <div>
                                    <h2 class="mb-0 text-2xl fw-bold tracking-tight">@yield('page-title', 'Dashboard')</h2>
                                    <small class="opacity-75">@yield('page-subtitle', 'Sistem Analisa Kebutuhan Pelatihan')</small>
                                </div>
                            </div>
                            <div class="text-end d-none d-sm-block">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-shield me-2"></i>
                                    <span>{{ $currentUser?->role_label ?? 'Mode Admin Demo' }}</span>
                                </div>
                                <small class="opacity-75">{{ now()->format('d F Y H:i') }} WIB</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Content -->
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireScripts
    <script>
        (function () {
            const body = document.body;
            const toggle = document.getElementById('sidebarToggle');
            const closeButtons = document.querySelectorAll('[data-sidebar-close]');
            const desktopQuery = window.matchMedia('(min-width: 992px)');

            function applyStoredState() {
                if (desktopQuery.matches && localStorage.getItem('tna-sidebar-collapsed') === '1') {
                    body.classList.add('sidebar-collapsed');
                }
            }

            function closeMobileSidebar() {
                body.classList.remove('sidebar-mobile-open');
            }

            applyStoredState();

            toggle?.addEventListener('click', function () {
                if (desktopQuery.matches) {
                    body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('tna-sidebar-collapsed', body.classList.contains('sidebar-collapsed') ? '1' : '0');
                    return;
                }

                body.classList.toggle('sidebar-mobile-open');
            });

            closeButtons.forEach(button => button.addEventListener('click', closeMobileSidebar));

            desktopQuery.addEventListener('change', function () {
                closeMobileSidebar();
                body.classList.remove('sidebar-collapsed');
                applyStoredState();
            });

        })();
    </script>
    @stack('scripts')
</body>
</html>
