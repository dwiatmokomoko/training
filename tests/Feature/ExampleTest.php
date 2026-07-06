<?php

namespace Tests\Feature;

use App\Http\Controllers\MasterCrudController;
use App\Models\AssessmentPeriod;
use App\Models\Employee;
use App\Models\SawCriterion;
use App\Models\SawScore;
use App\Models\User;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::query()->firstOrCreate(
            ['email' => 'admin@pn-sleman.go.id'],
            ['name' => 'Admin Test', 'role' => 'admin', 'password' => 'password']
        ));
    }

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_primary_tna_pages_are_available(): void
    {
        foreach ([
            '/master-data',
            '/nilai-saw',
            '/nilai-saw/create',
            '/penilaian-kinerja',
            '/analisis-tna',
            '/perencanaan-pelatihan',
            '/laporan',
            '/alur-penggunaan',
        ] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_master_crud_pages_are_available(): void
    {
        foreach (array_keys(MasterCrudController::resources()) as $resource) {
            $this->get("/master-data/{$resource}")->assertOk();
            $this->get("/master-data/{$resource}/create")->assertOk();
        }
    }

    public function test_employee_detail_is_available(): void
    {
        $employee = Employee::query()->firstOrFail();

        $this->get("/master-data/pegawai/{$employee->id}")
            ->assertOk()
            ->assertSee('Riwayat Pelatihan');
    }

    public function test_tna_report_can_be_exported(): void
    {
        $this->get('/laporan/export')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_manual_saw_score_can_be_saved(): void
    {
        $period = AssessmentPeriod::query()->firstOrFail();
        $employee = Employee::query()->firstOrFail();
        $criterion = SawCriterion::query()->firstOrFail();

        $this->post('/nilai-saw', [
            'assessment_period_id' => $period->id,
            'employee_id' => $employee->id,
            'saw_criterion_id' => $criterion->id,
            'value' => 4,
            'assessor_name' => 'Tester',
            'assessed_at' => now()->toDateString(),
            'notes' => 'Nilai manual untuk pegawai baru.',
        ])->assertRedirect();

        $this->assertTrue(SawScore::query()
            ->where('assessment_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->where('saw_criterion_id', $criterion->id)
            ->where('value', 4)
            ->exists());
    }

    public function test_pimpinan_cannot_open_input_routes(): void
    {
        $leader = User::query()->firstOrCreate(
            ['email' => 'pimpinan@pn-sleman.go.id'],
            ['name' => 'Pimpinan Test', 'role' => 'pimpinan', 'password' => 'password']
        );

        $this->actingAs($leader)
            ->get('/nilai-saw/create')
            ->assertForbidden();
    }
}
