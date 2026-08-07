<div class="tna-simple">
    @php
        $statusLabels = [
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
        ];

        $statusClasses = [
            'pending' => 'status-pending',
            'approved' => 'status-approved',
            'rejected' => 'status-rejected',
            'completed' => 'status-completed',
        ];
    @endphp

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="simple-filter">
        <h6>Filter Data</h6>
        <div class="filter-grid">
            <label>
                <span>Rumpun Jabatan</span>
                <select wire:model.live="jobFamilyFilter" class="form-select">
                    <option value="">Semua Rumpun</option>
                    @foreach($jobFamilies as $code => $label)
                        <option value="{{ $code }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Jenis Pelatihan</span>
                <select wire:model.live="trainingTypeFilter" class="form-select">
                    <option value="">Semua</option>
                    @foreach($trainingTypes as $trainingType)
                        <option value="{{ $trainingType }}">{{ $trainingType }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Periode</span>
                <select wire:model.live="periodFilter" class="form-select">
                    <option value="">Semua Periode</option>
                    @foreach($periods as $period)
                        <option value="{{ $period['key'] }}">{{ $period['label'] }}</option>
                    @endforeach
                </select>
            </label>
        </div>
        <div class="filter-actions">
            <button type="button" class="btn btn-outline-primary" wire:click="showFilteredData">
                <i class="fas fa-eye me-2"></i>
                Tampilkan
            </button>
            @if(\App\Support\Access::allows('analysis.run'))
                <button type="button" class="btn btn-primary" wire:click="runAnalysis" wire:loading.attr="disabled" wire:target="runAnalysis">
                    <span wire:loading.remove wire:target="runAnalysis">
                        <i class="fas fa-calculator me-2"></i>
                        Proses Analisis
                    </span>
                    <span wire:loading wire:target="runAnalysis">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Memproses...
                    </span>
                </button>
            @endif
        </div>
    </section>

    <section class="simple-summary">
        <div><span>Jumlah Pegawai</span><strong>{{ $summary['total_employees'] }}</strong></div>
        <div><span>Sudah Mengikuti Pelatihan</span><strong>{{ $summary['trained_employees'] }}</strong></div>
        <div><span>Belum Mengikuti Pelatihan</span><strong>{{ $summary['untrained_employees'] }}</strong></div>
        <div><span>Kuota Pelatihan</span><strong>{{ $summary['quota'] }} Orang</strong></div>
    </section>

    <div wire:loading.delay class="text-center text-muted py-4">
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        Memuat data...
    </div>

    <div wire:loading.remove>
        @if($trainingNeeds->count() > 0)
            @foreach($groups as $groupKey => $group)
                <section class="simple-group" wire:key="training-group-{{ $groupKey }}-{{ $jobFamilyFilter }}-{{ $trainingTypeFilter }}-{{ $periodFilter }}">
                    <div class="group-title">
                        <h5>{{ $group['label'] }}</h5>
                        <span>{{ $group['items']->count() }} rekomendasi pelatihan</span>
                    </div>

                    @if($group['items']->isNotEmpty())
                        <div class="table-responsive simple-table-shell">
                            <table class="table align-middle simple-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pegawai</th>
                                        <th>Jabatan</th>
                                        <th>Nilai SAW</th>
                                        <th>Prioritas</th>
                                        <th>Rekomendasi Pelatihan</th>
                                        <th>Status Kelayakan</th>
                                        <th>Tindak Lanjut</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group['items'] as $index => $need)
                                        @php
                                            $priorityLabel = match (true) {
                                                (float) $need->saw_score >= 0.85 => 'Sangat Tinggi',
                                                (float) $need->saw_score >= 0.70 => 'Tinggi',
                                                (float) $need->saw_score >= 0.55 => 'Sedang',
                                                default => 'Rendah',
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $need->employee->name }}</strong>
                                                <small>{{ $need->employee->nip }}</small>
                                            </td>
                                            <td>{{ $need->employee->position->name }}</td>
                                            <td><strong>{{ number_format((float) $need->saw_score, 4) }}</strong></td>
                                            <td>{{ $priorityLabel }}</td>
                                            <td>{{ $need->training_type }}</td>
                                            <td>
                                                <span class="simple-status {{ $need->eligibility_label === 'Layak' ? 'status-eligible' : 'status-reserve' }}">
                                                    {{ $need->eligibility_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="simple-status {{ $statusClasses[$need->status] ?? 'status-pending' }}">
                                                    {{ $statusLabels[$need->status] ?? ucfirst($need->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="simple-actions">
                                                    <a href="{{ route('training-needs.show', $need) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($need->status === 'pending' && \App\Support\Access::allows('training-needs.approve'))
                                                        <button type="button" class="btn btn-sm btn-outline-success" wire:click="updateStatus({{ $need->id }}, 'approved')" title="Setujui">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="updateStatus({{ $need->id }}, 'rejected')" title="Tolak">
                                                            <i class="fas fa-xmark"></i>
                                                        </button>
                                                    @endif
                                                    @if($need->status === 'approved' && \App\Support\Access::allows('training-needs.manage'))
                                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="updateStatus({{ $need->id }}, 'completed')" title="Selesai">
                                                            <i class="fas fa-flag-checkered"></i>
                                                        </button>
                                                    @endif
                                                    @if(\App\Support\Access::allows('training-needs.manage'))
                                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteTrainingNeed({{ $need->id }})" wire:confirm="Yakin ingin menghapus data ini?" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="simple-empty">Belum ada rekomendasi untuk filter ini.</div>
                    @endif
                </section>
            @endforeach
        @else
            <div class="simple-empty">
                Belum ada hasil analisis sesuai filter. Jalankan proses analisis SAW atau ubah filter data.
            </div>
        @endif
    </div>

    <style>
        .tna-simple {
            display: grid;
            gap: 1.5rem;
        }

        .simple-filter {
            padding-bottom: 1.25rem;
            border-bottom: 1px dashed var(--line);
        }

        .simple-filter h6 {
            margin: 0 0 1rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .filter-grid label {
            display: grid;
            gap: 0.4rem;
        }

        .filter-grid span,
        .simple-summary span,
        .group-title span {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .filter-actions {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .simple-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px dashed var(--line);
        }

        .simple-summary div {
            display: grid;
            gap: 0.25rem;
            padding: 0.8rem 0.9rem;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fbfcfd;
        }

        .simple-summary strong {
            color: var(--text-main);
            font-size: 1.25rem;
        }

        .simple-group {
            display: grid;
            gap: 0.8rem;
        }

        .group-title h5 {
            margin: 0;
            font-weight: 800;
            color: var(--text-main);
        }

        .simple-table-shell {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
        }

        .simple-table {
            margin: 0;
        }

        .simple-table th {
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 0.82rem;
            text-transform: uppercase;
            border-bottom: 1px solid var(--line);
        }

        .simple-table td {
            border-bottom: 1px solid #eef2f6;
        }

        .simple-table td small {
            display: block;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        .simple-status {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.58rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 800;
        }

        .status-pending {
            background: #fff3cd;
            color: #7a4d00;
        }

        .status-approved {
            background: var(--ma-light-green);
            color: var(--ma-dark-green);
        }

        .status-eligible {
            background: var(--ma-light-green);
            color: var(--ma-dark-green);
        }

        .status-reserve {
            background: var(--ma-light-yellow);
            color: #6f4e00;
        }

        .status-rejected {
            background: #fde2e2;
            color: #991b1b;
        }

        .status-completed {
            background: #e0f2fe;
            color: #075985;
        }

        .simple-actions {
            display: inline-flex;
            gap: 0.25rem;
        }

        .simple-empty {
            border: 1px dashed var(--line);
            border-radius: 8px;
            padding: 1.5rem;
            color: var(--text-muted);
            text-align: center;
            background: #fbfcfd;
        }

        @media (max-width: 991.98px) {
            .filter-grid,
            .simple-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            .filter-grid,
            .simple-summary {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
            }

            .filter-actions .btn {
                width: 100%;
            }
        }
    </style>
</div>
