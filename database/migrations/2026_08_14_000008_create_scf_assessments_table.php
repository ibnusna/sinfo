<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedInteger('assessor_user_id'); // user_id dari auth_gara
            $table->enum('assessor_type', ['guru', 'juri']);
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Satu assessor hanya bisa punya satu assessment per kelompok
            $table->unique(['group_id', 'assessor_user_id', 'assessor_type']);
            $table->index(['program_id', 'group_id', 'assessor_type']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('assessments');
    }
};
