<div class="dashboard-wrapper">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card employees-card">
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" wire:poll.5s>{{ $stats['total_employees'] }}</h3>
                    <p class="stats-label">Total Pegawai</p>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up"></i>
                        <span>+2.5%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card assessments-card">
                <div class="stats-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" wire:poll.5s>{{ $stats['total_assessments'] }}</h3>
                    <p class="stats-label">Total Penilaian</p>
                    <div class="stats-trend">
                        <i class="fas fa-arrow-up"></i>
                        <span>+15.3%</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card pending-card">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" wire:poll.5s>{{ $stats['pending_training'] }}</h3>
                    <p class="stats-label">Pelatihan Pending</p>
                    <div class="stats-trend warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Perlu Perhatian</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stats-card completed-card">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" wire:poll.5s>{{ $stats['completed_training'] }}</h3>
                    <p class="stats-label">Pelatihan Selesai</p>
                    <div class="stats-trend success">
                        <i class="fas fa-check"></i>
                        <span>Target Tercapai</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- SAW Analysis Section -->
        <div class="col-xl-8 col-lg-7">
            <div class="analysis-card">
                <div class="card-header-modern">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="header-text">
                            <h5 class="header-title">Analisis Simple Additive Weighting (SAW)</h5>
                            <p class="header-subtitle">Sistem Pendukung Keputusan Prioritas Pelatihan</p>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-outline-light btn-sm" wire:click="loadData">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="analysis-intro">
                        <div class="intro-content">
                            <h6 class="intro-title">Analisis Kebutuhan Pelatihan</h6>
                            <p class="intro-description">
                                Jalankan analisis SAW untuk menentukan prioritas kebutuhan pelatihan berdasarkan 
                                kriteria yang telah ditetapkan dengan bobot yang optimal.
                            </p>
                        </div>
                        
                        <div class="analysis-action">
                            <button wire:click="runAnalysis" 
                                    wire:loading.attr="disabled" 
                                    class="btn btn-primary btn-lg analysis-btn">
                                <span wire:loading.remove wire:target="runAnalysis">
                                    <i class="fas fa-play me-2"></i>
                                    Jalankan Analisis SAW
                                </span>
                                <span wire:loading wire:target="runAnalysis">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Menganalisis Data...
                                </span>
                            </button>
                        </div>
                    </div>

                    @if(count($recentTrainingNeeds) > 0)
                    <div class="results-section">
                        <div class="results-header">
                            <h6 class="results-title">
                                <i class="fas fa-trophy me-2"></i>
                                Top 10 Prioritas Pelatihan
                            </h6>
                            <span class="results-badge">{{ count($recentTrainingNeeds) }} Results</span>
                        </div>
                        
                        <div class="results-table-container">
                            <div class="table-responsive">
                                <table class="table results-table">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Pegawai</th>
                                            <th>Jabatan</th>
                                            <th>Pelatihan</th>
                                            <th>Skor</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentTrainingNeeds as $index => $need)
                                        <tr class="result-row">
                                            <td class="rank-cell">
                                                @if($need['priority_rank'] <= 3)
                                                    <span class="rank-badge gold">#{{ $need['priority_rank'] }}</span>
                                                @elseif($need['priority_rank'] <= 6)
                                                    <span class="rank-badge silver">#{{ $need['priority_rank'] }}</span>
                                                @else
                                                    <span class="rank-badge bronze">#{{ $need['priority_rank'] }}</span>
                                                @endif
                                            </td>
                                            <td class="employee-cell">
                                                <div class="employee-info">
                                                    <div class="employee-avatar">
                                                        {{ substr($need['employee']['name'], 0, 2) }}
                                                    </div>
                                                    <div class="employee-details">
                                                        <div class="employee-name">{{ $need['employee']['name'] }}</div>
                                                        <div class="employee-nip">{{ $need['employee']['nip'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="position-cell">
                                                <span class="position-tag">{{ $need['employee']['position']['name'] }}</span>
                                            </td>
                                            <td class="training-cell">
                                                <div class="training-type">{{ $need['training_type'] }}</div>
                                            </td>
                                            <td class="score-cell">
                                                <div class="score-container">
                                                    <div class="score-bar">
                                                        <div class="score-fill" style="width: {{ ($need['saw_score'] * 100) }}%"></div>
                                                    </div>
                                                    <span class="score-text">{{ number_format($need['saw_score'], 4) }}</span>
                                                </div>
                                            </td>
                                            <td class="status-cell">
                                                @if($need['status'] === 'pending')
                                                    <span class="status-tag pending">Pending</span>
                                                @elseif($need['status'] === 'approved')
                                                    <span class="status-tag approved">Disetujui</span>
                                                @elseif($need['status'] === 'completed')
                                                    <span class="status-tag completed">Selesai</span>
                                                @else
                                                    <span class="status-tag rejected">Ditolak</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="results-footer">
                            <a href="{{ route('training-needs.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>
                                Lihat Semua Hasil
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
            <!-- Criteria Weights -->
            <div class="criteria-card mb-4">
                <div class="card-header-modern">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-weight-hanging"></i>
                        </div>
                        <div class="header-text">
                            <h5 class="header-title">Bobot Kriteria SAW</h5>
                            <p class="header-subtitle">Distribusi Bobot Penilaian</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    @if(count($criteriaWeights) > 0)
                        <div class="criteria-list">
                            @foreach($criteriaWeights as $criteria)
                            <div class="criteria-item">
                                <div class="criteria-info">
                                    <span class="criteria-name">{{ $criteria['name'] }}</span>
                                    <span class="criteria-weight">{{ number_format($criteria['weight'] * 100, 1) }}%</span>
                                </div>
                                <div class="criteria-bar">
                                    <div class="criteria-fill" style="width: {{ $criteria['weight'] * 100 }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-criteria">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Belum ada kriteria yang didefinisikan</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="actions-card">
                <div class="card-header-modern">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="header-text">
                            <h5 class="header-title">Aksi Cepat</h5>
                            <p class="header-subtitle">Shortcut Menu Utama</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="actions-grid">
                        <a href="{{ route('employees.create') }}" class="action-item">
                            <div class="action-icon employees">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-content">
                                <h6 class="action-title">Tambah Pegawai</h6>
                                <p class="action-desc">Input data pegawai baru</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('assessments.create') }}" class="action-item">
                            <div class="action-icon assessments">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <h6 class="action-title">Input Penilaian</h6>
                                <p class="action-desc">Tambah penilaian pegawai</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                        
                        <a href="{{ route('training-needs.report') }}" class="action-item">
                            <div class="action-icon reports">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="action-content">
                                <h6 class="action-title">Unduh Laporan</h6>
                                <p class="action-desc">Export hasil analisis</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="info-card">
                <div class="card-header-modern">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="header-text">
                            <h5 class="header-title">Tentang Sistem TNA</h5>
                            <p class="header-subtitle">Informasi Metode dan Tujuan Sistem</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body-modern">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="info-section">
                                <h6 class="info-title">
                                    <i class="fas fa-calculator me-2"></i>
                                    Metode Simple Additive Weighting (SAW)
                                </h6>
                                <p class="info-description">
                                    Metode SAW digunakan untuk menentukan prioritas kebutuhan pelatihan berdasarkan kriteria terbobot:
                                </p>
                                <ul class="info-list">
                                    <li><span class="list-weight">30%</span> Kompetensi Teknis</li>
                                    <li><span class="list-weight">25%</span> Kinerja Pegawai</li>
                                    <li><span class="list-weight">20%</span> Pengalaman Kerja</li>
                                    <li><span class="list-weight">15%</span> Tingkat Pendidikan</li>
                                    <li><span class="list-weight">10%</span> Usia</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="info-section">
                                <h6 class="info-title">
                                    <i class="fas fa-target me-2"></i>
                                    Tujuan Sistem
                                </h6>
                                <p class="info-description">
                                    Sistem ini membantu Mahkamah Agung dalam:
                                </p>
                                <ul class="info-list">
                                    <li>Mengidentifikasi kebutuhan pelatihan pegawai</li>
                                    <li>Memprioritaskan pelatihan berdasarkan analisis objektif</li>
                                    <li>Meningkatkan kompetensi SDM secara terstruktur</li>
                                    <li>Mengoptimalkan alokasi anggaran pelatihan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Styles and Scripts inside the component -->
    <style>
        /* Modern Dashboard Styles */
        .dashboard-wrapper {
            padding: 0;
        }

        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 20px;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--ma-green), var(--ma-yellow));
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            flex-shrink: 0;
        }

        .employees-card .stats-icon {
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
        }

        .assessments-card .stats-icon {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .pending-card .stats-icon {
            background: linear-gradient(135deg, #ffc107, #ffb300);
        }

        .completed-card .stats-icon {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .stats-content {
            flex: 1;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            line-height: 1;
        }

        .stats-label {
            color: #6c757d;
            margin: 5px 0 10px 0;
            font-weight: 500;
        }

        .stats-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .stats-trend.success {
            color: #28a745;
        }

        .stats-trend.warning {
            color: #ffc107;
        }

        /* Analysis Card */
        .analysis-card, .criteria-card, .actions-card, .info-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .analysis-card:hover, .criteria-card:hover, .actions-card:hover, .info-card:hover {
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .card-header-modern {
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
            color: white;
            padding: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .header-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .header-subtitle {
            margin: 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .card-body-modern {
            padding: 30px;
        }

        /* Analysis Section */
        .analysis-intro {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .intro-title {
            color: var(--ma-dark-green);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .intro-description {
            color: #6c757d;
            margin: 0;
            line-height: 1.5;
        }

        .analysis-btn {
            border-radius: 15px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
            border: none;
            transition: all 0.3s ease;
        }

        .analysis-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34,139,34,0.3);
        }

        /* Results Section */
        .results-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #f1f3f4;
        }

        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .results-title {
            color: var(--ma-dark-green);
            font-weight: 600;
            margin: 0;
        }

        .results-badge {
            background: linear-gradient(135deg, var(--ma-yellow), var(--ma-dark-yellow));
            color: #333;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .results-table-container {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .results-table {
            margin: 0;
        }

        .results-table th {
            border: none;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 15px 10px;
        }

        .result-row {
            transition: all 0.3s ease;
        }

        .result-row:hover {
            background-color: rgba(34,139,34,0.05);
        }

        .result-row td {
            border: none;
            padding: 15px 10px;
            vertical-align: middle;
        }

        .rank-badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .rank-badge.gold {
            background: linear-gradient(135deg, #ffd700, #ffb300);
            color: #333;
        }

        .rank-badge.silver {
            background: linear-gradient(135deg, #c0c0c0, #a8a8a8);
            color: #333;
        }

        .rank-badge.bronze {
            background: linear-gradient(135deg, #cd7f32, #b8860b);
            color: white;
        }

        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .employee-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95rem;
        }

        .employee-nip {
            color: #6c757d;
            font-size: 0.8rem;
        }

        .position-tag {
            background: linear-gradient(135deg, var(--ma-yellow), var(--ma-dark-yellow));
            color: #333;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .training-type {
            font-weight: 600;
            color: var(--ma-dark-green);
            font-size: 0.9rem;
        }

        .score-container {
            text-align: center;
        }

        .score-bar {
            width: 60px;
            height: 6px;
            background: #e9ecef;
            border-radius: 10px;
            margin: 0 auto 5px;
            overflow: hidden;
        }

        .score-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--ma-green), var(--ma-yellow));
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .score-text {
            font-weight: 600;
            color: var(--ma-dark-green);
            font-size: 0.85rem;
        }

        .status-tag {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-tag.pending {
            background: linear-gradient(135deg, #ffc107, #ffb300);
            color: #333;
        }

        .status-tag.approved {
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
            color: white;
        }

        .status-tag.completed {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .status-tag.rejected {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .results-footer {
            text-align: center;
        }

        /* Criteria Section */
        .criteria-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .criteria-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .criteria-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .criteria-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .criteria-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .criteria-weight {
            font-weight: 700;
            color: var(--ma-dark-green);
        }

        .criteria-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .criteria-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--ma-green), var(--ma-yellow));
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        /* Actions Section */
        .actions-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .action-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .action-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
            text-decoration: none;
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .action-icon.employees {
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
        }

        .action-icon.assessments {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .action-icon.reports {
            background: linear-gradient(135deg, #ffc107, #ffb300);
        }

        .action-content {
            flex: 1;
        }

        .action-title {
            margin: 0 0 5px 0;
            font-weight: 600;
            color: #2c3e50;
        }

        .action-desc {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .action-arrow {
            color: #6c757d;
            font-size: 1.2rem;
        }

        /* Info Section */
        .info-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            height: 100%;
        }

        .info-title {
            color: var(--ma-dark-green);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .info-description {
            color: #6c757d;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            padding: 8px 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-list li::before {
            content: '✓';
            color: var(--ma-green);
            font-weight: bold;
            width: 20px;
        }

        .list-weight {
            background: linear-gradient(135deg, var(--ma-yellow), var(--ma-dark-yellow));
            color: #333;
            padding: 4px 8px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.8rem;
            min-width: 40px;
            text-align: center;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .analysis-intro {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            
            .analysis-action {
                text-align: center;
            }
        }

        @media (max-width: 768px) {
            .stats-card {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .stats-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .card-header-modern {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .header-content {
                flex-direction: column;
                gap: 10px;
            }
            
            .card-body-modern {
                padding: 20px;
            }
            
            .results-table-container {
                padding: 15px;
            }
            
            .results-table {
                font-size: 0.9rem;
            }
            
            .employee-info {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .dashboard-wrapper {
                padding: 0 10px;
            }
            
            .stats-card {
                padding: 20px;
            }
            
            .analysis-btn {
                width: 100%;
                padding: 12px 20px;
                font-size: 1rem;
            }
            
            .results-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .criteria-item, .action-item, .info-section {
                padding: 15px;
            }
        }
    </style>

    <script>
        // Auto refresh setiap 30 detik
        setInterval(function() {
            @this.call('loadData');
        }, 30000);

        // Listen untuk event analysis completed
        window.addEventListener('analysisCompleted', event => {
            // Show success notification
            const toast = document.createElement('div');
            toast.className = 'toast-notification success';
            toast.innerHTML = '<i class="fas fa-check-circle"></i> Analisis SAW berhasil dijalankan!';
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
            
            // Refresh halaman setelah 2 detik
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        });

        // Toast notification styles
        const toastStyles = `
        <style>
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transform: translateX(400px);
            transition: all 0.3s ease;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .toast-notification.show {
            transform: translateX(0);
        }

        .toast-notification.success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        </style>
        `;

        document.head.insertAdjacentHTML('beforeend', toastStyles);
    </script>
</div>