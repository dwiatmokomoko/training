<div class="training-needs-container">
    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" 
                       wire:model.live="search" 
                       class="form-control search-input" 
                       placeholder="Cari pegawai atau jenis pelatihan...">
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <select wire:model.live="statusFilter" class="form-select modern-select">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
                <option value="completed">Selesai</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-6 mb-3">
            <button wire:click="$refresh" class="btn btn-outline-primary w-100 refresh-btn">
                <i class="fas fa-sync-alt me-2"></i>
                Refresh
            </button>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="d-flex gap-2">
                <button class="btn btn-success flex-fill export-btn">
                    <i class="fas fa-download me-2"></i>
                    Export
                </button>
                <button class="btn btn-info flex-fill print-btn">
                    <i class="fas fa-print me-2"></i>
                    Print
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="loading-container">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat data...</p>
        </div>
    </div>

    <!-- Training Needs Cards/Table -->
    <div wire:loading.remove>
        @if($trainingNeeds->count() > 0)
        
        <!-- Mobile Card View -->
        <div class="d-lg-none">
            <div class="row">
                @foreach($trainingNeeds as $need)
                <div class="col-12 mb-3">
                    <div class="card training-card {{ $need->priority_rank <= 5 ? 'priority-high' : '' }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="priority-badge">
                                    @if($need->priority_rank <= 3)
                                        <span class="badge bg-danger priority-rank">#{{ $need->priority_rank }}</span>
                                    @elseif($need->priority_rank <= 10)
                                        <span class="badge bg-warning priority-rank">#{{ $need->priority_rank }}</span>
                                    @else
                                        <span class="badge bg-secondary priority-rank">#{{ $need->priority_rank }}</span>
                                    @endif
                                </div>
                                <div class="status-badge">
                                    @if($need->status === 'pending')
                                        <span class="badge status-pending">Pending</span>
                                    @elseif($need->status === 'approved')
                                        <span class="badge status-approved">Disetujui</span>
                                    @elseif($need->status === 'completed')
                                        <span class="badge status-completed">Selesai</span>
                                    @else
                                        <span class="badge status-rejected">Ditolak</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="employee-info mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-circle me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 employee-name">{{ $need->employee->name }}</h6>
                                        <small class="text-muted">{{ $need->employee->nip }}</small>
                                    </div>
                                </div>
                                <span class="badge position-badge">{{ $need->employee->position->name }}</span>
                            </div>
                            
                            <div class="training-info mb-3">
                                <h6 class="training-type">{{ $need->training_type }}</h6>
                                <p class="training-desc text-muted small">{{ Str::limit($need->training_description, 80) }}</p>
                            </div>
                            
                            <div class="score-section mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Skor SAW</span>
                                    <span class="fw-bold score-value">{{ number_format($need->saw_score, 4) }}</span>
                                </div>
                                <div class="progress score-progress">
                                    <div class="progress-bar" style="width: {{ ($need->saw_score * 100) }}%"></div>
                                </div>
                            </div>
                            
                            <div class="card-actions">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('training-needs.show', $need) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($need->status === 'pending')
                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#updateStatusModal{{ $need->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endif
                                    <button wire:click="deleteTrainingNeed({{ $need->id }})" 
                                            wire:confirm="Yakin ingin menghapus data ini?"
                                            class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="d-none d-lg-block">
            <div class="table-responsive modern-table">
                <table class="table table-hover">
                    <thead class="table-header">
                        <tr>
                            <th class="priority-col">Prioritas</th>
                            <th class="employee-col">Pegawai</th>
                            <th class="position-col">Jabatan</th>
                            <th class="training-col">Jenis Pelatihan</th>
                            <th class="score-col">Skor SAW</th>
                            <th class="status-col">Status</th>
                            <th class="date-col">Tanggal</th>
                            <th class="action-col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trainingNeeds as $need)
                        <tr class="table-row {{ $need->priority_rank <= 5 ? 'priority-row' : '' }}">
                            <td class="priority-cell">
                                @if($need->priority_rank <= 3)
                                    <span class="badge bg-danger priority-badge-lg">#{{ $need->priority_rank }}</span>
                                @elseif($need->priority_rank <= 10)
                                    <span class="badge bg-warning priority-badge-lg">#{{ $need->priority_rank }}</span>
                                @else
                                    <span class="badge bg-secondary priority-badge-lg">#{{ $need->priority_rank }}</span>
                                @endif
                            </td>
                            <td class="employee-cell">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="employee-name">{{ $need->employee->name }}</div>
                                        <small class="text-muted">{{ $need->employee->nip }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="position-cell">
                                <span class="badge position-badge">{{ $need->employee->position->name }}</span>
                            </td>
                            <td class="training-cell">
                                <div class="training-type">{{ $need->training_type }}</div>
                                <small class="text-muted">{{ Str::limit($need->training_description, 50) }}</small>
                            </td>
                            <td class="score-cell">
                                <div class="score-container">
                                    <div class="progress score-progress mb-1">
                                        <div class="progress-bar" style="width: {{ ($need->saw_score * 100) }}%"></div>
                                    </div>
                                    <small class="score-value">{{ number_format($need->saw_score, 4) }}</small>
                                </div>
                            </td>
                            <td class="status-cell">
                                @if($need->status === 'pending')
                                    <span class="badge status-pending">Pending</span>
                                @elseif($need->status === 'approved')
                                    <span class="badge status-approved">Disetujui</span>
                                @elseif($need->status === 'completed')
                                    <span class="badge status-completed">Selesai</span>
                                @else
                                    <span class="badge status-rejected">Ditolak</span>
                                @endif
                            </td>
                            <td class="date-cell">
                                @if($need->recommended_date)
                                    <span class="date-text">{{ $need->recommended_date->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="action-cell">
                                <div class="btn-group action-buttons" role="group">
                                    <a href="{{ route('training-needs.show', $need) }}" 
                                       class="btn btn-outline-info btn-sm action-btn" 
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($need->status === 'pending')
                                    <button type="button" 
                                            class="btn btn-outline-success btn-sm action-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#updateStatusModal{{ $need->id }}" 
                                            title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endif
                                    <button wire:click="deleteTrainingNeed({{ $need->id }})" 
                                            wire:confirm="Yakin ingin menghapus data ini?"
                                            class="btn btn-outline-danger btn-sm action-btn" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-container mt-4">
            {{ $trainingNeeds->links() }}
        </div>
        
        @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h5 class="empty-title">
                @if($search || $statusFilter)
                    Tidak ada data yang sesuai dengan filter
                @else
                    Belum ada analisis kebutuhan pelatihan
                @endif
            </h5>
            <p class="empty-description">
                @if($search || $statusFilter)
                    Coba ubah filter pencarian atau status
                @else
                    Klik tombol "Jalankan Analisis SAW" untuk memulai analisis kebutuhan pelatihan.
                @endif
            </p>
            @if(!$search && !$statusFilter)
            <button class="btn btn-primary empty-action">
                <i class="fas fa-calculator me-2"></i>
                Jalankan Analisis SAW
            </button>
            @endif
        </div>
        @endif
    </div>

    <!-- Summary Cards -->
    @if($trainingNeeds->total() > 0)
    <div class="summary-section mt-5">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="summary-card priority-card">
                    <div class="summary-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="summary-content">
                        <h4 class="summary-number">{{ $trainingNeeds->where('priority_rank', '<=', 5)->count() }}</h4>
                        <p class="summary-label">Prioritas Tinggi</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="summary-card pending-card">
                    <div class="summary-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="summary-content">
                        <h4 class="summary-number">{{ $trainingNeeds->where('status', 'pending')->count() }}</h4>
                        <p class="summary-label">Menunggu Persetujuan</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="summary-card approved-card">
                    <div class="summary-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="summary-content">
                        <h4 class="summary-number">{{ $trainingNeeds->where('status', 'approved')->count() }}</h4>
                        <p class="summary-label">Disetujui</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="summary-card score-card">
                    <div class="summary-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="summary-content">
                        <h4 class="summary-number">{{ number_format($trainingNeeds->avg('saw_score'), 3) }}</h4>
                        <p class="summary-label">Rata-rata Skor SAW</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modals for Status Update -->
    @foreach($trainingNeeds as $need)
    <div class="modal fade" id="updateStatusModal{{ $need->id }}" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Update Status Pelatihan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="employee-info-modal mb-3">
                        <strong>{{ $need->employee->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $need->training_type }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status{{ $need->id }}">
                            <option value="pending" {{ $need->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $need->status === 'approved' ? 'selected' : '' }}>Disetujui</option>
                            <option value="rejected" {{ $need->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="completed" {{ $need->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" id="notes{{ $need->id }}" rows="3" placeholder="Tambahkan catatan...">{{ $need->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" 
                            class="btn btn-primary"
                            onclick="updateStatus{{ $need->id }}()">
                        <i class="fas fa-save me-2"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Styles and Scripts inside the component -->
    <style>
        /* Modern Training Needs Styles */
        .training-needs-container {
            padding: 0;
        }

        /* Search Box */
        .search-box {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ma-green);
            z-index: 10;
        }

        .search-input {
            padding-left: 45px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--ma-green);
            box-shadow: 0 0 0 0.2rem rgba(34, 139, 34, 0.25);
        }

        /* Modern Select */
        .modern-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .modern-select:focus {
            border-color: var(--ma-green);
            box-shadow: 0 0 0 0.2rem rgba(34, 139, 34, 0.25);
        }

        /* Buttons */
        .refresh-btn, .export-btn, .print-btn {
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .refresh-btn:hover, .export-btn:hover, .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Loading */
        .loading-container {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            margin: 20px 0;
        }

        .loading-spinner .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Training Cards (Mobile) */
        .training-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .training-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .training-card.priority-high {
            border-left: 5px solid #dc3545;
        }

        .avatar-circle {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .employee-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .training-type {
            font-weight: 600;
            color: var(--ma-dark-green);
            margin-bottom: 8px;
        }

        .training-desc {
            line-height: 1.4;
        }

        /* Priority Badges */
        .priority-rank, .priority-badge-lg {
            font-size: 0.9rem;
            font-weight: 700;
            padding: 8px 12px;
            border-radius: 20px;
        }

        /* Status Badges */
        .status-pending {
            background: linear-gradient(135deg, #ffc107, #ffb300);
            color: #333;
        }

        .status-approved {
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
            color: white;
        }

        .status-completed {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .status-rejected {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .position-badge {
            background: linear-gradient(135deg, var(--ma-yellow), var(--ma-dark-yellow));
            color: #333;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 15px;
        }

        /* Score Progress */
        .score-progress {
            height: 8px;
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .score-progress .progress-bar {
            background: linear-gradient(90deg, var(--ma-green), var(--ma-yellow));
            border-radius: 10px;
        }

        .score-value {
            color: var(--ma-dark-green);
            font-weight: 700;
        }

        /* Modern Table */
        .modern-table {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .table-header th {
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
            color: white;
            font-weight: 600;
            border: none;
            padding: 20px 15px;
            text-align: center;
        }

        .table-row {
            transition: all 0.3s ease;
        }

        .table-row:hover {
            background-color: rgba(34, 139, 34, 0.05);
            transform: scale(1.01);
        }

        .table-row.priority-row {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .table td {
            padding: 20px 15px;
            vertical-align: middle;
            border: none;
            border-bottom: 1px solid #f1f3f4;
        }

        /* Action Buttons */
        .action-buttons .action-btn {
            border-radius: 8px;
            margin: 0 2px;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 20px;
            margin: 20px 0;
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--ma-green);
            margin-bottom: 20px;
        }

        .empty-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .empty-description {
            color: #6c757d;
            margin-bottom: 30px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .empty-action {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
        }

        /* Summary Cards */
        .summary-section {
            margin-top: 40px;
        }

        .summary-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .summary-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .priority-card .summary-icon {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .pending-card .summary-icon {
            background: linear-gradient(135deg, #ffc107, #ffb300);
        }

        .approved-card .summary-icon {
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
        }

        .score-card .summary-icon {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .summary-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .summary-label {
            color: #6c757d;
            margin: 0;
            font-weight: 500;
        }

        /* Modern Modal */
        .modern-modal {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modern-modal .modal-header {
            background: linear-gradient(135deg, var(--ma-green), var(--ma-dark-green));
            color: white;
            border: none;
            padding: 25px;
        }

        .modern-modal .modal-body {
            padding: 30px;
        }

        .modern-modal .modal-footer {
            border: none;
            padding: 20px 30px;
            background-color: #f8f9fa;
        }

        .employee-info-modal {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px;
            border-radius: 12px;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
        }

        .pagination-container .pagination {
            gap: 5px;
        }

        .pagination-container .page-link {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            color: var(--ma-green);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination-container .page-link:hover {
            background-color: var(--ma-green);
            border-color: var(--ma-green);
            color: white;
            transform: translateY(-2px);
        }

        .pagination-container .page-item.active .page-link {
            background-color: var(--ma-green);
            border-color: var(--ma-green);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .training-card {
                margin-bottom: 20px;
            }
            
            .summary-card {
                padding: 20px;
                gap: 15px;
            }
            
            .summary-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .summary-number {
                font-size: 1.5rem;
            }
            
            .search-input {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            .training-needs-container {
                padding: 0 10px;
            }
            
            .summary-card {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .card-actions .btn-group {
                flex-direction: column;
                gap: 8px;
            }
            
            .card-actions .btn {
                width: 100%;
            }
        }
    </style>

    <script>
        @foreach($trainingNeeds as $need)
        function updateStatus{{ $need->id }}() {
            const status = document.getElementById('status{{ $need->id }}').value;
            const notes = document.getElementById('notes{{ $need->id }}').value;
            
            @this.call('updateStatus', {{ $need->id }}, status, notes);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('updateStatusModal{{ $need->id }}'));
            modal.hide();
        }
        @endforeach
    </script>
</div>