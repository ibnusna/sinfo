<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->enum('type', ['juklak', 'juknis']);
            $table->string('title');
            $table->longText('content')->nullable(); // Markdown/rich text
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->unsignedInteger('created_by'); // user_id dari auth_gara
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['program_id', 'type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('documents');
    }
};
