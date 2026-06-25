<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_families', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('work_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('work_units')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->foreignId('job_family_id')->nullable()->after('id')->constrained('job_families')->nullOnDelete();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('work_unit_id')->nullable()->after('position_id')->constrained('work_units')->nullOnDelete();
            $table->date('current_position_start_date')->nullable()->after('work_experience');
            $table->date('last_promotion_date')->nullable()->after('current_position_start_date');
            $table->date('last_training_date')->nullable()->after('last_promotion_date');
        });

        Schema::table('criteria', function (Blueprint $table) {
            $table->string('code', 5)->nullable()->unique()->after('id');
            $table->unsignedTinyInteger('importance_rating')->nullable()->after('type');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE criteria ALTER COLUMN weight TYPE NUMERIC(6,3)');
        }
    }

    public function down(): void
    {
        Schema::table('criteria', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn(['code', 'importance_rating']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE criteria ALTER COLUMN weight TYPE NUMERIC(3,2)');
        }

        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('work_unit_id');
            $table->dropColumn([
                'current_position_start_date',
                'last_promotion_date',
                'last_training_date',
            ]);
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_family_id');
        });

        Schema::dropIfExists('work_units');
        Schema::dropIfExists('job_families');
    }
};
