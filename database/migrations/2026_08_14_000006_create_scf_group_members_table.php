<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedInteger('student_user_id'); // user_id dari auth_gara
            $table->timestamp('created_at')->useCurrent();

            // Satu siswa hanya bisa di satu kelompok per program
            // Validasi dilakukan di application layer karena cross-database
            $table->unique(['group_id', 'student_user_id']);
            $table->index(['student_user_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('group_members');
    }
};
