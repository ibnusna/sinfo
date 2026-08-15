<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('assessment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->string('criterion', 255); // nama kriteria penilaian
            $table->decimal('score', 5, 2)->default(0); // nilai (0-100)
            $table->text('note')->nullable(); // catatan per kriteria
            $table->timestamps();

            $table->foreign('assessment_id')
                ->references('id')
                ->on('assessments')
                ->onDelete('cascade');

            $table->index(['assessment_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('assessment_details');
    }
};
