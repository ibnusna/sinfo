<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('assessment_criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable(); // null = global, non-null = per program
            // assessment_type: poster (guru menilai poster), science (guru menilai IPA), food (juri menilai makanan)
            $table->enum('assessment_type', ['poster', 'science', 'food']);
            $table->string('name', 255);
            $table->text('description')->nullable();
            // weight dalam persentase (0-100), total semua kriteria dalam satu tipe harus = 100
            $table->decimal('weight', 5, 2)->default(0);
            // max_score: nilai maksimum yang bisa diberikan per kriteria (default 100)
            $table->decimal('max_score', 5, 2)->default(100);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['program_id', 'assessment_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('assessment_criteria');
    }
};
