<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Koneksi SCF — JANGAN jalankan terhadap auth_gara
     */
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('academic_year', 20); // contoh: 2026/2027
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('programs');
    }
};
