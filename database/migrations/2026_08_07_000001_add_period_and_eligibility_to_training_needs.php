<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_needs', function (Blueprint $table) {
            $table->unsignedSmallInteger('period_year')->nullable()->after('recommended_date');
            $table->unsignedTinyInteger('period_semester')->nullable()->after('period_year');
            $table->string('period_label', 30)->nullable()->after('period_semester');
            $table->string('eligibility_status', 30)->nullable()->after('priority_rank');
        });

        $year = (int) now()->year;
        $semester = now()->month <= 6 ? 1 : 2;

        DB::table('training_needs')->update([
            'period_year' => $year,
            'period_semester' => $semester,
            'period_label' => $year . ' Semester ' . $semester,
        ]);

        DB::table('training_needs')
            ->where('saw_score', '>', 0.9)
            ->update(['eligibility_status' => 'layak']);

        DB::table('training_needs')
            ->where('saw_score', '<=', 0.9)
            ->update(['eligibility_status' => 'cadangan']);
    }

    public function down(): void
    {
        Schema::table('training_needs', function (Blueprint $table) {
            $table->dropColumn([
                'period_year',
                'period_semester',
                'period_label',
                'eligibility_status',
            ]);
        });
    }
};
