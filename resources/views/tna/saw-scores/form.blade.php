@extends('layouts.app', [
    'title' => $score ? 'Edit Nilai SAW' : 'Tambah Nilai SAW',
    'subtitle' => 'Input nilai kriteria untuk pegawai. Gunakan skala sesuai master kriteria.',
])

@section('content')
    <div class="mb-5">
        <a href="{{ route('tna.saw-scores.index', ['period' => old('assessment_period_id', $score?->assessment_period_id ?? ($defaults['assessment_period_id'] ?? null))]) }}" class="btn-secondary">Kembali ke Nilai SAW</a>
    </div>

    <section class="grid gap-6 xl:grid-cols-[1fr_.8fr]">
        <div class="panel">
            <form method="POST" action="{{ $score ? route('tna.saw-scores.update', $score) : route('tna.saw-scores.store') }}" class="grid gap-5 md:grid-cols-2">
                @csrf
                @if ($score)
                    @method('PUT')
                @endif

                @php
                    $default = fn ($key, $fallback = null) => old($key, $score?->{$key} ?? ($defaults[$key] ?? $fallback));
                @endphp

                <label class="form-label">
                    Periode
                    <select name="assessment_period_id" class="form-input" required>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected((string) $default('assessment_period_id') === (string) $period->id)>{{ $period->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="form-label">
                    Pegawai
                    <select name="employee_id" class="form-input" required>
                        <option value="">- Pilih Pegawai -</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @selected((string) $default('employee_id') === (string) $employee->id)>{{ $employee->name }} - {{ $employee->nip }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="form-label md:col-span-2">
                    Kriteria SAW
                    <select name="saw_criterion_id" class="form-input" required>
                        <option value="">- Pilih Kriteria -</option>
                        @foreach ($criteria as $criterion)
                            <option value="{{ $criterion->id }}" @selected((string) $default('saw_criterion_id') === (string) $criterion->id)>
                                {{ $criterion->code }} - {{ $criterion->name }} ({{ $criterion->attribute }}, skala {{ $criterion->scale_min }}-{{ $criterion->scale_max }})
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="form-label">
                    Nilai
                    <input name="value" type="number" min="0" max="100" step="0.01" value="{{ $default('value') }}" class="form-input" required>
                </label>

                <label class="form-label">
                    Tanggal Penilaian
                    <input name="assessed_at" type="date" value="{{ $default('assessed_at') instanceof \Carbon\CarbonInterface ? $default('assessed_at')->toDateString() : $default('assessed_at') }}" class="form-input">
                </label>

                <label class="form-label">
                    Nama Penilai
                    <input name="assessor_name" value="{{ $default('assessor_name') }}" class="form-input" placeholder="Atasan/SDM/Pimpinan unit">
                </label>

                <label class="form-label md:col-span-2">
                    Catatan
                    <textarea name="notes" rows="4" class="form-input" placeholder="Contoh: nilai awal pegawai baru, hasil assessment, atau koreksi data otomatis">{{ $default('notes') }}</textarea>
                </label>

                <div class="flex flex-wrap gap-3 border-t border-stone-100 pt-5 md:col-span-2">
                    <button class="btn-primary" type="submit">Simpan Nilai</button>
                    <a href="{{ route('tna.saw-scores.index', ['period' => $default('assessment_period_id')]) }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>

        <aside class="panel">
            <div class="panel-header">
                <div>
                    <h3>Panduan Input</h3>
                    <p>Nilai manual lebih diprioritaskan daripada nilai otomatis.</p>
                </div>
            </div>
            <div class="grid gap-3 text-sm text-stone-700">
                <p><strong>Pegawai baru:</strong> pilih periode, pegawai, lalu input semua kriteria aktif yang diperlukan.</p>
                <p><strong>Kriteria Cost:</strong> nilai lebih kecil akan lebih baik setelah normalisasi, misalnya C1 kinerja atau C5 usia.</p>
                <p><strong>Kriteria Benefit:</strong> nilai lebih besar akan lebih diprioritaskan, misalnya lama tidak pelatihan atau masa jabatan.</p>
                <p>Jika nilai manual dihapus, sistem kembali memakai nilai otomatis/default agar analisis tetap bisa berjalan.</p>
            </div>
        </aside>
    </section>
@endsection
