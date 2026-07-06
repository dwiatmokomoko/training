<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('work_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->foreignId('employee_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('level')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30)->unique();
            $table->string('name');
            $table->string('gender', 20)->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('employee_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->date('position_started_at')->nullable();
            $table->string('rank_class', 30)->nullable();
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });

        Schema::create('position_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->string('history_type')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 60)->unique();
            $table->string('name');
            $table->foreignId('employee_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->nullable();
            $table->text('target_competency')->nullable();
            $table->string('provider')->nullable();
            $table->unsignedSmallInteger('duration_hours')->nullable();
            $table->string('method')->default('Klasikal');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('training_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_id')->nullable()->constrained()->nullOnDelete();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->string('provider')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('result')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('performance_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('code', 60)->unique();
            $table->foreignId('employee_group_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('weight', 8, 4)->default(1);
            $table->unsignedTinyInteger('scale_min')->default(1);
            $table->unsignedTinyInteger('scale_max')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('saw_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->enum('attribute', ['Benefit', 'Cost']);
            $table->decimal('weight', 8, 4);
            $table->unsignedTinyInteger('scale_min')->default(1);
            $table->unsignedTinyInteger('scale_max')->default(5);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name');
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->string('status')->default('Draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('performance_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performance_indicator_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->text('notes')->nullable();
            $table->string('assessor_nip', 30)->nullable();
            $table->date('assessed_at')->nullable();
            $table->timestamps();
            $table->unique(['assessment_period_id', 'employee_id', 'performance_indicator_id'], 'perf_unique_score');
        });

        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_period_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('year');
            $table->string('status')->default('Draft');
            $table->date('planned_at')->nullable();
            $table->decimal('estimated_budget', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('training_plan_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('saw_score', 8, 4)->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->string('priority_class')->nullable();
            $table->string('approval_status')->default('Diajukan');
            $table->timestamps();
            $table->unique(['training_plan_id', 'employee_id', 'training_id'], 'plan_participant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_plan_participants');
        Schema::dropIfExists('training_plans');
        Schema::dropIfExists('performance_scores');
        Schema::dropIfExists('assessment_periods');
        Schema::dropIfExists('saw_criteria');
        Schema::dropIfExists('performance_indicators');
        Schema::dropIfExists('training_histories');
        Schema::dropIfExists('trainings');
        Schema::dropIfExists('position_histories');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('work_units');
        Schema::dropIfExists('employee_groups');
    }
};
