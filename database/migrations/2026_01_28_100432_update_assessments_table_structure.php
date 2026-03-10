<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Drop old columns
            $table->dropForeign(['criteria_id']);
            $table->dropColumn(['criteria_id', 'score']);
            
            // Add new columns
            $table->decimal('total_score', 5, 4)->default(0)->after('assessment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Restore old columns
            $table->foreignId('criteria_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2);
            
            // Drop new columns
            $table->dropColumn('total_score');
        });
    }
};
