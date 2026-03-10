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
            --ma-green: #228B22;
            --ma-dark-green: #006400;
            --ma-light-green: #32CD32;
            --ma-yellow: #FFD700;
            --ma-dark-yellow: #FFA500;
            --ma-light-yellow: #FFFF99;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--ma-green) 0%, var(--ma-dark-green) 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,215,0,0.2);
            border-left-color: var(--ma-yellow);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link i {
            color: var(--ma-yellow);
            width: 20px;
        }
        
        .main-content {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            transition: all 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--ma-green) 0%, var(--ma-dark-green) 100%);
            color: white;
            border-radius: 1rem 1rem 0 0 !important;
            border-bottom: 3px solid var(--ma-yellow);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--ma-green) 0%, var(--ma-dark-green) 100%);
            border: none;
            border-radius: 0.5rem;
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
            padding: 1rem 0;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-radius: 0 0 1rem 1rem;
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="logo-ma me-2">MA</div>
                            <div>
                                <h6 class="text-white mb-0">TNA System</h6>
                                <small class="text-white-50">Mahkamah Agung</small>
                            </div>
                        </div>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                        <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                            <i class="fas fa-users me-2"></i>
                            Data Pegawai
                        </a>
                        <a class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}" href="{{ route('assessments.index') }}">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Penilaian
                        </a>
                        <a class="nav-link {{ request()->routeIs('training-needs.*') ? 'active' : '' }}" href="{{ route('training-needs.index') }}">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Kebutuhan Pelatihan
                        </a>
                        <a class="nav-link" href="{{ route('training-needs.report') }}">
                            <i class="fas fa-chart-bar me-2"></i>
                            Laporan
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <!-- Header -->
                    <div class="header-gradient">
                        <div class="container-fluid">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="mb-0">@yield('page-title', 'Dashboard')</h2>
                                    <small class="opacity-75">@yield('page-subtitle', 'Sistem Analisa Kebutuhan Pelatihan')</small>
                                </div>
                                <div class="text-end">
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireScripts
    @stack('scripts')
</body>
</html>