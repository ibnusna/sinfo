<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['program_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('settings');
    }
};
