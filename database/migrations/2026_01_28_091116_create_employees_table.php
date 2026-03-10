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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->enum('education_level', ['SMA', 'D3', 'S1', 'S2', 'S3']);
            $table->integer('work_experience')->default(0); // dalam tahun
            $table->date('birth_date');
            $table->enum('gender', ['L', 'P']);
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
