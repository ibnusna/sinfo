<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedInteger('user_id'); // user_id dari auth_gara
            $table->string('action', 100);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['program_id', 'created_at']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('activity_logs');
    }
};
