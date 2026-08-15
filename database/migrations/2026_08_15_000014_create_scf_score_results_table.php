<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('score_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedInteger('student_user_id'); // user_id dari auth_gara

            // Komponen nilai kelompok
            $table->decimal('poster_score', 8, 4)->nullable();
            $table->decimal('science_score', 8, 4)->nullable();
            $table->decimal('food_score', 8, 4)->nullable();
            $table->decimal('group_score', 8, 4)->nullable();

            // Bobot yang digunakan saat kalkulasi (snapshot konfigurasi)
            $table->decimal('poster_weight_used', 5, 2)->nullable();
            $table->decimal('science_weight_used', 5, 2)->nullable();
            $table->decimal('food_weight_used', 5, 2)->nullable();

            // Nilai individu
            $table->decimal('contribution_factor', 5, 4)->nullable();
            $table->decimal('individual_score', 8, 4)->nullable();

            // Status kelompok saat snapshot ini dibuat
            // incomplete: belum semua komponen selesai
            // calculated: semua komponen selesai, nilai sudah dihitung
            // finalized: guru telah memfinalisasi, nilai terkunci
            $table->enum('group_status', ['incomplete', 'calculated', 'finalized'])->default('incomplete');

            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->unsignedInteger('finalized_by')->nullable(); // user_id guru

            $table->timestamps();

            // Satu baris per siswa per kelompok (diupdate saat recalculate)
            $table->unique(['group_id', 'student_user_id']);
            $table->index(['program_id', 'group_id']);
            $table->index(['program_id', 'group_status']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('score_results');
    }
};
