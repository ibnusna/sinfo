<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('member_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedInteger('student_user_id'); // user_id dari auth_gara
            // true = berkontribusi penuh, false = tidak/berkurang
            $table->boolean('is_contributed')->default(true);
            // wajib diisi jika is_contributed = false
            $table->text('reason')->nullable();
            // user_id ketua yang mengisi kontribusi ini
            $table->unsignedInteger('submitted_by');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Satu entry per anggota per kelompok
            $table->unique(['group_id', 'student_user_id']);
            $table->index(['program_id', 'group_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('member_contributions');
    }
};
