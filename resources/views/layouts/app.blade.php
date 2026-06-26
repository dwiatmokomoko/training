<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Analisa Kebutuhan Pelatihan - Mahkamah Agung')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        :root {
            --ma-green: #1f7a3a;
            --ma-dark-green: #11532a;
            --ma-light-green: #e8f5ec;
            --ma-yellow: #f2c94c;
            --ma-dark-yellow: #d89d10;
            --ma-light-yellow: #fff7d6;
            --surface: #ffffff;
            --surface-muted: #f5f7f8;
            --line: #dfe5e8;
            --text-main: #1f2933;
            --text-muted: #667085;
            --shadow-sm: 0 2px 10px rgba(15, 23, 42, 0.06);
            --shadow-md: 0 10px 24px rgba(15, 23, 42, 0.10);
        }

        body {
            color: var(--text-main);
            background: var(--surface-muted);
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
            background: linear-gradient(180deg, var(--ma-dark-green) 0%, #163f2a 100%);
            box-shadow: 2px 0 16px rgba(15, 23, 42, 0.14);
            position: sticky;
            top: 0;
            overflow-y: auto;
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
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 0.8rem 1rem;
            margin: 0.25rem 0;
            border-radius: 8px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            font-weight: 600;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.12);
            border-left-color: var(--ma-yellow);
        }
        
        .sidebar .nav-link i {
            color: var(--ma-yellow);
            width: 20px;
        }
        
        .main-content {
            background: linear-gradient(180deg, #f7faf8 0%, #eef3f0 100%);
            min-height: 100vh;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
        }
        
        .card:hover {
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--ma-green) 0%, var(--ma-dark-green) 100%);
            color: white;
            border-radius: 8px 8px 0 0 !important;
            border-bottom: 2px solid var(--ma-yellow);
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--ma-green) 0%, var(--ma-dark-green) 100%);
            border: none;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--ma-dark-green) 0%, var(--ma-green) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(34,139,34,0.3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--ma-yellow) 0%, var(--ma-dark-yellow) 100%);
            border: none;
            color: #333;
            font-weight: 600;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, var(--ma-dark-yellow) 0%, var(--ma-yellow) 100%);
            color: #333;
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
            width: 40px;
            height: 40px;
            background: var(--ma-yellow);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ma-green);
            font-weight: bold;
            font-size: 18px;
        }
        
        .header-gradient {
            background: linear-gradient(135deg, var(--ma-green) 0%, var(--ma-dark-green) 100%);
            color: white;
            padding: 1.15rem 0;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-radius: 0 0 8px 8px;
        }

        .sidebar-toggle {
            width: 42px;
            height: 42px;
            border: 1px solid rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.12);
            color: white;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(255,255,255,0.2);
            color: white;
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
<body>
    <div class="app-shell">
        <div class="sidebar-overlay" data-sidebar-close></div>
        <aside class="sidebar-shell">
            <div class="sidebar p-3">
                <div class="sidebar-brand mb-4">
                    <div class="d-flex align-items-center">
                        <div class="logo-ma me-2">MA</div>
                        <div>
                            <h6 class="text-white mb-0">TNA System</h6>
                            <small class="text-white-50">Mahkamah Agung</small>
                        </div>
                    </div>
                    <button type="button" class="sidebar-close" data-sidebar-close aria-label="Tutup menu">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <nav class="nav flex-column">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('system-flow') ? 'active' : '' }}" href="{{ route('system-flow') }}">
                        <i class="fas fa-route me-2"></i>
                        Alur Sistem
                    </a>
                    <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                        <i class="fas fa-users me-2"></i>
                        Data Pegawai
                    </a>
                    <a class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}" href="{{ route('assessments.index') }}">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Penilaian
                    </a>
                    <a class="nav-link {{ request()->routeIs('training-needs.index', 'training-needs.show') ? 'active' : '' }}" href="{{ route('training-needs.index') }}">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Kebutuhan Pelatihan
                    </a>
                    <a class="nav-link {{ request()->routeIs('training-needs.report') ? 'active' : '' }}" href="{{ route('training-needs.report') }}">
                        <i class="fas fa-chart-bar me-2"></i>
                        Laporan
                    </a>
                </nav>
            </div>
        </aside>

        <main class="content-shell">
            <div class="main-content p-4">
                <!-- Header -->
                <div class="header-gradient">
                    <div class="container-fluid">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Tampilkan atau sembunyikan sidebar">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <div>
                                    <h2 class="mb-0">@yield('page-title', 'Dashboard')</h2>
                                    <small class="opacity-75">@yield('page-subtitle', 'Sistem Analisa Kebutuhan Pelatihan')</small>
                                </div>
                            </div>
                            <div class="text-end d-none d-sm-block">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <span>{{ now()->format('d F Y') }}</span>
                                </div>
                                <small class="opacity-75">{{ now()->format('H:i') }} WIB</small>
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
