<?php

namespace App\Http\Controllers;

use App\Models\AssessmentPeriod;
use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\PerformanceIndicator;
use App\Models\Position;
use App\Models\PositionHistory;
use App\Models\SawCriterion;
use App\Models\Training;
use App\Models\TrainingHistory;
use App\Models\WorkUnit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class MasterCrudController extends Controller
{
    public function index(string $resource)
    {
        $config = $this->config($resource);

        return view('masters.crud.index', [
            'resource' => $resource,
            'config' => $config,
            'records' => $config['model']::query()
                ->with($config['with'] ?? [])
                ->orderBy($config['order_by'] ?? 'id')
                ->get(),
        ]);
    }

    public function create(string $resource)
    {
        return view('masters.crud.form', [
            'resource' => $resource,
            'config' => $this->config($resource),
            'record' => null,
            'options' => $this->options(),
        ]);
    }

    public function show(string $resource, int $id)
    {
        abort_unless($resource === 'pegawai', 404);

        return view('masters.employees.show', [
            'employee' => Employee::query()
                ->with([
                    'group',
                    'unit',
                    'position',
                    'trainingHistories.training',
                    'performanceScores.indicator',
                    'sawScores.period',
                    'sawScores.criterion',
                ])
                ->findOrFail($id),
        ]);
    }

    public function store(Request $request, string $resource)
    {
        $config = $this->config($resource);
        $data = $this->validatedData($request, $config);

        $config['model']::query()->create($data);

        return redirect()->route('masters.crud.index', $resource)->with('status', "{$config['singular']} berhasil ditambahkan.");
    }

    public function edit(string $resource, int $id)
    {
        $config = $this->config($resource);

        return view('masters.crud.form', [
            'resource' => $resource,
            'config' => $config,
            'record' => $config['model']::query()->findOrFail($id),
            'options' => $this->options(),
        ]);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $config = $this->config($resource);
        $record = $config['model']::query()->findOrFail($id);
        $data = $this->validatedData($request, $config, $record->id);

        $record->update($data);

        return redirect()->route('masters.crud.index', $resource)->with('status', "{$config['singular']} berhasil diperbarui.");
    }

    public function destroy(string $resource, int $id)
    {
        $config = $this->config($resource);
        $record = $config['model']::query()->findOrFail($id);

        try {
            $record->delete();
        } catch (QueryException) {
            return back()->withErrors("{$config['singular']} tidak dapat dihapus karena masih dipakai oleh data lain.");
        }

        return back()->with('status', "{$config['singular']} berhasil dihapus.");
    }

    public static function resources(): array
    {
        return [
            'rumpun' => [
                'label' => 'Rumpun Pegawai',
                'singular' => 'Rumpun pegawai',
                'model' => EmployeeGroup::class,
                'order_by' => 'code',
                'columns' => [
                    'code' => 'Kode',
                    'name' => 'Nama Rumpun',
                    'description' => 'Deskripsi',
                    'is_active' => 'Status',
                ],
                'fields' => [
                    ['name' => 'code', 'label' => 'Kode Rumpun', 'type' => 'text', 'rules' => ['required', 'max:20', 'unique:employee_groups,code']],
                    ['name' => 'name', 'label' => 'Nama Rumpun', 'type' => 'text', 'rules' => ['required', 'max:255']],
                    ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'rules' => ['nullable']],
                    ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'rules' => ['boolean']],
                ],
            ],
            'unit-kerja' => [
                'label' => 'Unit Kerja',
                'singular' => 'Unit kerja',
                'model' => WorkUnit::class,
                'order_by' => 'code',
                'columns' => [
                    'code' => 'Kode',
                    'name' => 'Nama Unit',
                    'description' => 'Keterangan',
                    'is_active' => 'Status',
                ],
                'fields' => [
                    ['name' => 'code', 'label' => 'Kode Unit', 'type' => 'text', 'rules' => ['required', 'max:30', 'unique:work_units,code']],
                    ['name' => 'name', 'label' => 'Nama Unit', 'type' => 'text', 'rules' => ['required', 'max:255']],
                    ['name' => 'description', 'label' => 'Keterangan', 'type' => 'textarea', 'rules' => ['nullable']],
                    ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'rules' => ['boolean']],
                ],
            ],
            'jabatan' => [
                'label' => 'Jabatan',
                'singular' => 'Jabatan',
                'model' => Position::class,
                'with' => ['group', 'unit'],
                'order_by' => 'name',
                'columns' => [
                    'code' => 'Kode',
                    'name' => 'Nama Jabatan',
                    'group.name' => 'Rumpun',
                    'unit.name' => 'Unit',
                    'level' => 'Level',
                    'type' => 'Jenis',
                    'is_active' => 'Status',
                ],
                'fields' => [
                    ['name' => 'code', 'label' => 'Kode Jabatan', 'type' => 'text', 'rules' => ['required', 'max:50', 'unique:positions,code']],
                    ['name' => 'name', 'label' => 'Nama Jabatan', 'type' => 'text', 'rules' => ['required', 'max:255']],
                    ['name' => 'employee_group_id', 'label' => 'Rumpun', 'type' => 'select', 'options' => 'groups', 'rules' => ['nullable', 'exists:employee_groups,id']],
                    ['name' => 'work_unit_id', 'label' => 'Unit Kerja', 'type' => 'select', 'options' => 'units', 'rules' => ['nullable', 'exists:work_units,id']],
                    ['name' => 'level', 'label' => 'Level Jabatan', 'type' => 'text', 'rules' => ['nullable', 'max:255']],
                    ['name' => 'type', 'label' => 'Jenis Jabatan', 'type' => 'text', 'rules' => ['nullable', 'max:255']],
                    ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'rules' => ['boolean']],
                ],
            ],
            'pegawai' => [
                'label' => 'Pegawai',
                'singular' => 'Pegawai',
                'model' => Employee::class,
                'with' => ['group', 'unit', 'position'],
                'order_by' => 'name',
                'columns' => [
                    'nip' => 'NIP',
                    'name' => 'Nama',
                    'gender' => 'Jenis Kelamin',
                    'position.name' => 'Jabatan',
                    'unit.name' => 'Unit',
                    'status' => 'Status',
                ],
                'fields' => [
                    ['name' => 'nip', 'label' => 'NIP', 'type' => 'text', 'rules' => ['required', 'max:30', 'unique:employees,nip']],
                    ['name' => 'name', 'label' => 'Nama Pegawai', 'type' => 'text', 'rules' => ['required', 'max:255']],
                    ['name' => 'gender', 'label' => 'Jenis Kelamin', 'type' => 'select_static', 'choices' => ['Laki-Laki' => 'Laki-Laki', 'Perempuan' => 'Perempuan'], 'rules' => ['nullable', 'max:20']],
                    ['name' => 'birth_place', 'label' => 'Tempat Lahir', 'type' => 'text', 'rules' => ['nullable', 'max:255']],
                    ['name' => 'birth_date', 'label' => 'Tanggal Lahir', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => ['nullable', 'email', 'max:255']],
                    ['name' => 'phone', 'label' => 'No HP', 'type' => 'text', 'rules' => ['nullable', 'max:255']],
                    ['name' => 'employee_group_id', 'label' => 'Rumpun', 'type' => 'select', 'options' => 'groups', 'rules' => ['nullable', 'exists:employee_groups,id']],
                    ['name' => 'work_unit_id', 'label' => 'Unit Kerja', 'type' => 'select', 'options' => 'units', 'rules' => ['nullable', 'exists:work_units,id']],
                    ['name' => 'position_id', 'label' => 'Jabatan', 'type' => 'select', 'options' => 'positions', 'rules' => ['nullable', 'exists:positions,id']],
                    ['name' => 'position_started_at', 'label' => 'TMT Jabatan', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    ['name' => 'rank_class', 'label' => 'Golongan', 'type' => 'text', 'rules' => ['nullable', 'max:30']],
                    ['name' => 'status', 'label' => 'Status Pegawai', 'type' => 'select_static', 'choices' => ['Aktif' => 'Aktif', 'Tidak Aktif' => 'Tidak Aktif'], 'rules' => ['required', 'max:255']],
                ],
            ],
            'pelatihan' => [
                'label' => 'Pelatihan',
                'singular' => 'Pelatihan',
                'model' => Training::class,
                'with' => ['group'],
                'order_by' => 'name',
                'columns' => [
                    'code' => 'Kode',
                    'name' => 'Nama Pelatihan',
                    'group.name' => 'Rumpun',
                    'category' => 'Kategori',
                    'method' => 'Metode',
                    'is_active' => 'Status',
                ],
                'fields' => [
                    ['name' => 'code', 'label' => 'Kode Pelatihan', 'type' => 'text', 'rules' => ['required', 'max:60', 'unique:trainings,code']],
                    ['name' => 'name', 'label' => 'Nama Pelatihan', 'type' => 'text', 'rules' => ['required', 'max:255']],
                    ['name' => 'employee_group_id', 'label' => 'Rumpun', 'type' => 'select', 'options' => 'groups', 'rules' => ['nullable', 'exists:employee_groups,id']],
                    ['name' => 'category', 'label' => 'Kategori', 'type' => 'text', 'rules' => ['nullable', 'max:255']],
                    ['name' => 'target_competency', 'label' => 'Kompetensi Tujuan', 'type' => 'textarea', 'rules' => ['nullable']],
                    ['name' => 'provider', 'label' => 'Penyelenggara', 'type' => 'text', 'rules' => ['nullable', 'max:255']],
                    ['name' => 'duration_hours', 'label' => 'Durasi Jam', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
                    ['name' => 'method', 'label' => 'Metode', 'type' => 'select_static', 'choices' => ['Klasikal' => 'Klasikal', 'E-learning' => 'E-learning', 'Coaching' => 'Coaching', 'Blended' => 'Blended'], 'rules' => ['required', 'max:255']],
                    ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'rules' => ['boolean']],
                ],
            ],
            'indikator-kinerja' => [
                'label' => 'Indikator Kinerja',
                'singular' => 'Indikator kinerja',
                'model' => PerformanceIndicator::class,
                'with' => ['group'],
                'order_by' => 'code',
                'columns' => [
                    'code' => 'Kode',
                    'name' => 'Indikator',
                    'group.name' => 'Rumpun',
                    'weight' => 'Bobot',
                    'scale_min' => 'Min',
                    'scale_max' => 'Max',
                    'is_active' => 'Status',
                ],
                'fields' => [
                    ['name' => 'code', 'label' => 'Kode Indikator', 'type' => 'text', 'rules' => ['required', 'max:60', 'unique:performance_indicators,code']],
                    ['name' => 'employee_group_id', 'label' => 'Rumpun', 'type' => 'select', 'options' => 'groups', 'rules' => ['nullable', 'exists:employee_groups,id']],
                    ['name' => 'name', 'label' => 'Nama Indikator', 'type' => 'text', 'rules' => ['required', 'max:255']],
                    ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'rules' => ['nullable']],
                    ['name' => 'weight', 'label' => 'Bobot Indikator', 'type' => 'number_decimal', 'rules' => ['required', 'numeric', 'min:0']],
                    ['name' => 'scale_min', 'label' => 'Skala Minimum', 'type' => 'number', 'rules' => ['required', 'integer', 'min:1']],
                    ['name' => 'scale_max', 'label' => 'Skala Maksimum', 'type' => 'number', 'rules' => ['required', 'integer', 'min:1']],
                    ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'rules' => ['boolean']],
                ],
            ],
            'kriteria-saw' => [
                'label' => 'Kriteria SAW',
                'singular' => 'Kriteria SAW',
                'model' => SawCriterion::class,
                'order_by' => 'code',
                'columns' => [
                    'code' => 'Kode',
                    'name' => 'Kriteria',
                    'attribute' => 'Atribut',
                    'weight' => 'Bobot',
                    'scale_min' => 'Min',
                    'scale_max' => 'Max',
                    'is_active' => 'Status',
                ],
                'fields' => [
                    ['name' => 'code', 'label' => 'Kode Kriteria', 'type' => 'text', 'rules' => ['required', 'max:20', 'unique:saw_criteria,code']],
                    ['name' => 'name', 'label' => 'Nama Kriteria', 'type' => 'text', 'rules' => ['required', 'max:255']],
                    ['name' => 'attribute', 'label' => 'Atribut', 'type' => 'select_static', 'choices' => ['Benefit' => 'Benefit', 'Cost' => 'Cost'], 'rules' => ['required', Rule::in(['Benefit', 'Cost'])]],
                    ['name' => 'weight', 'label' => 'Bobot', 'type' => 'number_decimal', 'rules' => ['required', 'numeric', 'min:0']],
                    ['name' => 'scale_min', 'label' => 'Skala Minimum', 'type' => 'number', 'rules' => ['required', 'integer', 'min:1']],
                    ['name' => 'scale_max', 'label' => 'Skala Maksimum', 'type' => 'number', 'rules' => ['required', 'integer', 'min:1']],
                    ['name' => 'description', 'label' => 'Keterangan', 'type' => 'textarea', 'rules' => ['nullable']],
                    ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'rules' => ['boolean']],
                ],
            ],
            'periode' => [
                'label' => 'Periode Penilaian',
                'singular' => 'Periode penilaian',
                'model' => AssessmentPeriod::class,
                'order_by' => 'code',
                'columns' => [
                    'code' => 'Kode',
                    'name' => 'Periode',
                    'started_at' => 'Mulai',
                    'ended_at' => 'Selesai',
                    'status' => 'Status',
                ],
                'fields' => [
                    ['name' => 'code', 'label' => 'Kode Periode', 'type' => 'text', 'rules' => ['required', 'max:40', 'unique:assessment_periods,code']],
                    ['name' => 'name', 'label' => 'Nama Periode', 'type' => 'text', 'rules' => ['required', 'max:255']],
                    ['name' => 'started_at', 'label' => 'Tanggal Mulai', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    ['name' => 'ended_at', 'label' => 'Tanggal Selesai', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    ['name' => 'status', 'label' => 'Status', 'type' => 'select_static', 'choices' => ['Draft' => 'Draft', 'Berjalan' => 'Berjalan', 'Dikunci' => 'Dikunci', 'Selesai' => 'Selesai'], 'rules' => ['required', 'max:255']],
                    ['name' => 'notes', 'label' => 'Keterangan', 'type' => 'textarea', 'rules' => ['nullable']],
                ],
            ],
            'riwayat-jabatan' => [
                'label' => 'Riwayat Jabatan',
                'singular' => 'Riwayat jabatan',
                'model' => PositionHistory::class,
                'with' => ['employee', 'position'],
                'order_by' => 'started_at',
                'columns' => [
                    'employee.name' => 'Pegawai',
                    'position.name' => 'Jabatan',
                    'started_at' => 'Mulai',
                    'ended_at' => 'Selesai',
                    'history_type' => 'Jenis',
                ],
                'fields' => [
                    ['name' => 'employee_id', 'label' => 'Pegawai', 'type' => 'select', 'options' => 'employees', 'rules' => ['required', 'exists:employees,id']],
                    ['name' => 'position_id', 'label' => 'Jabatan', 'type' => 'select', 'options' => 'positions', 'rules' => ['nullable', 'exists:positions,id']],
                    ['name' => 'started_at', 'label' => 'Tanggal Mulai', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    ['name' => 'ended_at', 'label' => 'Tanggal Selesai', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    ['name' => 'history_type', 'label' => 'Jenis Riwayat', 'type' => 'text', 'rules' => ['nullable', 'max:255']],
                    ['name' => 'notes', 'label' => 'Keterangan', 'type' => 'textarea', 'rules' => ['nullable']],
                ],
            ],
            'riwayat-pelatihan' => [
                'label' => 'Riwayat Pelatihan',
                'singular' => 'Riwayat pelatihan',
                'model' => TrainingHistory::class,
                'with' => ['employee', 'training'],
                'order_by' => 'started_at',
                'columns' => [
                    'employee.name' => 'Pegawai',
                    'training.name' => 'Pelatihan',
                    'started_at' => 'Mulai',
                    'ended_at' => 'Selesai',
                    'result' => 'Hasil',
                    'certificate_number' => 'Sertifikat',
                ],
                'fields' => [
                    ['name' => 'employee_id', 'label' => 'Pegawai', 'type' => 'select', 'options' => 'employees', 'rules' => ['required', 'exists:employees,id']],
                    ['name' => 'training_id', 'label' => 'Pelatihan', 'type' => 'select', 'options' => 'trainings', 'rules' => ['nullable', 'exists:trainings,id']],
                    ['name' => 'started_at', 'label' => 'Tanggal Mulai', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    ['name' => 'ended_at', 'label' => 'Tanggal Selesai', 'type' => 'date', 'rules' => ['nullable', 'date']],
                    ['name' => 'provider', 'label' => 'Penyelenggara', 'type' => 'text', 'rules' => ['nullable', 'max:255']],
                    ['name' => 'certificate_number', 'label' => 'Nomor Sertifikat', 'type' => 'text', 'rules' => ['nullable', 'max:255']],
                    ['name' => 'result', 'label' => 'Hasil', 'type' => 'select_static', 'choices' => ['Lulus' => 'Lulus', 'Tidak Lulus' => 'Tidak Lulus', 'Belum Mengikuti' => 'Belum Mengikuti'], 'rules' => ['nullable', 'max:255']],
                    ['name' => 'notes', 'label' => 'Keterangan', 'type' => 'textarea', 'rules' => ['nullable']],
                ],
            ],
        ];
    }

    public static function resourceCards(): array
    {
        return collect(self::resources())
            ->map(fn (array $config, string $key) => [
                'key' => $key,
                'label' => $config['label'],
                'model' => $config['model'],
            ])
            ->values()
            ->all();
    }

    private function config(string $resource): array
    {
        abort_unless(array_key_exists($resource, self::resources()), 404);

        return self::resources()[$resource];
    }

    private function options(): array
    {
        return [
            'groups' => EmployeeGroup::query()->orderBy('name')->pluck('name', 'id')->all(),
            'units' => WorkUnit::query()->orderBy('name')->pluck('name', 'id')->all(),
            'positions' => Position::query()->orderBy('name')->pluck('name', 'id')->all(),
            'employees' => Employee::query()->orderBy('name')->pluck('name', 'id')->all(),
            'trainings' => Training::query()->orderBy('name')->pluck('name', 'id')->all(),
        ];
    }

    private function validatedData(Request $request, array $config, ?int $ignoreId = null): array
    {
        $rules = [];
        $checkboxes = [];

        foreach ($config['fields'] as $field) {
            $fieldRules = $field['rules'] ?? ['nullable'];

            if ($ignoreId) {
                $fieldRules = array_map(function ($rule) use ($ignoreId) {
                    if (is_string($rule) && str_starts_with($rule, 'unique:')) {
                        return $rule.','.$ignoreId;
                    }

                    return $rule;
                }, $fieldRules);
            }

            $rules[$field['name']] = $fieldRules;

            if (($field['type'] ?? null) === 'checkbox') {
                $checkboxes[] = $field['name'];
            }
        }

        $data = $request->validate($rules);

        foreach ($checkboxes as $checkbox) {
            $data[$checkbox] = $request->boolean($checkbox);
        }

        return Arr::where($data, fn ($value) => $value !== '');
    }
}
