<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'scf';

    public function up(): void
    {
        Schema::connection('scf')->create('landing_photos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('section', ['hero', 'about', 'gallery'])->default('gallery');
            $table->string('category')->nullable()->default('exhibition');
            $table->string('image_path');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section', 'is_active', 'sort_order']);
        });

        // Seed initial photos if table is newly created
        $now = now();
        DB::connection('scf')->table('landing_photos')->insert([
            [
                'title' => 'Foto Utama Hero Section',
                'section' => 'hero',
                'category' => null,
                'image_path' => 'foto/hero.jpg',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Foto Tentang Program SFF',
                'section' => 'about',
                'category' => null,
                'image_path' => 'foto/about.jpg',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Exhibition Day Science Food Festival 1',
                'section' => 'gallery',
                'category' => 'exhibition',
                'image_path' => 'foto/gallery-1.jpg',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Persiapan Stan Makanan',
                'section' => 'gallery',
                'category' => 'preparation',
                'image_path' => 'foto/gallery-2.jpg',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Sesi Penilaian & Presentasi',
                'section' => 'gallery',
                'category' => 'presentation',
                'image_path' => 'foto/gallery-3.jpg',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Pameran Karya & Poster Infografis',
                'section' => 'gallery',
                'category' => 'exhibition',
                'image_path' => 'foto/gallery-4.jpg',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Dokumentasi Kelompok Peserta',
                'section' => 'gallery',
                'category' => 'preparation',
                'image_path' => 'foto/gallery-5.jpg',
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::connection('scf')->dropIfExists('landing_photos');
    }
};
