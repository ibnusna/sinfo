<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedInteger('user_id'); // user_id dari auth_gara.users (int, bukan bigint)
            $table->enum('assignment_type', ['guru', 'juri']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Satu user hanya boleh satu assignment per program
            $table->unique(['program_id', 'user_id']);
            $table->index(['program_id', 'assignment_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('assignments');
    }
};
