<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('product_metadata', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('group_id');
            $table->string('product_name', 255);
            $table->text('description')->nullable();
            $table->text('ingredients')->nullable();
            // Kandungan gizi — raw data dari ketua, tidak dikarang sistem
            $table->text('carbohydrate')->nullable();
            $table->text('protein')->nullable();
            $table->text('fat')->nullable();
            $table->text('other_nutrients')->nullable(); // vitamin, mineral, dll
            $table->unsignedInteger('created_by'); // user_id ketua dari auth_gara
            $table->timestamps();

            $table->unique(['program_id', 'group_id']); // satu metadata per kelompok per program
            $table->index(['group_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('product_metadata');
    }
};
