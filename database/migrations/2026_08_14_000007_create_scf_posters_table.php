<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('posters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedInteger('uploaded_by'); // user_id dari auth_gara (harus leader)
            $table->string('original_name', 255);
            $table->string('file_path', 500); // path relatif di storage
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size'); // bytes
            $table->enum('status', ['pending', 'submitted', 'revision', 'approved'])->default('pending');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->unsignedInteger('verified_by')->nullable(); // user_id dari auth_gara
            $table->timestamps();

            $table->index(['program_id', 'group_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('posters');
    }
};
