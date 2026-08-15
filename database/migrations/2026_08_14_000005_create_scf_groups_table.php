<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->string('name', 100);
            $table->unsignedInteger('leader_user_id')->nullable(); // user_id siswa dari auth_gara
            $table->unsignedInteger('created_by'); // user_id guru dari auth_gara
            $table->timestamps();

            $table->index(['program_id']);
            $table->index(['leader_user_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('groups');
    }
};
