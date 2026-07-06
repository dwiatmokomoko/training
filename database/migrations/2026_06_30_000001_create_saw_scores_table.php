<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saw_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('saw_criterion_id')->constrained()->cascadeOnDelete();
            $table->decimal('value', 8, 2);
            $table->string('source')->default('Manual');
            $table->text('notes')->nullable();
            $table->string('assessor_name')->nullable();
            $table->date('assessed_at')->nullable();
            $table->timestamps();
            $table->unique(['assessment_period_id', 'employee_id', 'saw_criterion_id'], 'saw_score_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saw_scores');
    }
};
