<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScfProgram;
use App\Models\ScfSetting;
use App\Models\ScfAssignment;
use App\Models\ScfAssessmentCriterion;
use App\Models\User;

class ScfSeeder extends Seeder
{
    /**
     * Seeder hanya untuk data SCF.
     * TIDAK membuat user baru di auth_gara.
     * Menggunakan user_id yang sudah ada di auth_gara.
     */
    public function run(): void
    {
        $this->command->info('=== SCF Seeder ===');

        // ─────────────────────────────────
        // 1. Buat Program SCF
        // ─────────────────────────────────
        $program = ScfProgram::firstOrCreate(
            ['academic_year' => '2026/2027'],
            [
                'name'         => 'Science Food Festival',
                'status'       => 'active',
                'start_at'     => '2026-08-01 00:00:00',
                'end_at'       => '2026-12-31 23:59:59',
            ]
        );

        $this->command->info("✓ Program: {$program->name} ({$program->academic_year})");

        // ─────────────────────────────────
        // 2. Konfigurasi System (default: open)
        // ─────────────────────────────────
        ScfSetting::updateOrCreate(
            ['program_id' => $program->id, 'key' => 'system_status'],
            ['value' => 'open']
        );
        $this->command->info('✓ System status: open');

        // ─────────────────────────────────
        // 3. Score Weight Settings (belum ditentukan — null)
        // Operator dapat mengubah ini via UI.
        // ─────────────────────────────────
        $scoreSettings = [
            // Bobot komponen nilai kelompok (dalam %, total harus 100)
            // null = belum ditentukan, sistem akan gunakan equal weight sebagai fallback
            'poster_weight'  => null,
            'science_weight' => null,
            'food_weight'    => null,

            // Contribution factor values
            // full = 1.00 (selalu)
            'contribution_factor_full'    => '1.00',
            // reduced & none = belum ditentukan, operator yang mengisi
            'contribution_factor_reduced' => null,
            'contribution_factor_none'    => null,
        ];

        foreach ($scoreSettings as $key => $value) {
            ScfSetting::firstOrCreate(
                ['program_id' => $program->id, 'key' => $key],
                ['value' => $value]
            );
        }
        $this->command->info('✓ Score settings keys created (bobot belum ditentukan, dikonfigurasi operator)');

        // ─────────────────────────────────
        // 4. Assessment Criteria — WAJIB sesuai spesifikasi
        // Hapus criteria lama yang mungkin salah, seed ulang yang benar.
        // ─────────────────────────────────
        // Hapus semua criteria global (program_id = null) yang sudah ada
        ScfAssessmentCriterion::whereNull('program_id')->delete();

        $this->command->info('✓ Menghapus criteria lama...');

        // ── SCIENCE CRITERIA (Guru — Penilaian IPA) ──────────────────────
        // Total weight: 30 + 25 + 20 + 15 + 10 = 100
        $scienceCriteria = [
            [
                'name'        => 'Penguasaan Materi IPA',
                'description' => 'Menilai pemahaman siswa terhadap struktur tubuh manusia, fungsi organ, sistem pencernaan, makanan, dan hubungan materi dengan produk.',
                'weight'      => 30.00,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Keterkaitan dengan Makanan',
                'description' => 'Menilai kemampuan menghubungkan produk dengan kandungan makanan, konsep zat makanan, sistem pencernaan, dan kesehatan.',
                'weight'      => 25.00,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Kejelasan Penyampaian',
                'description' => 'Menilai alur presentasi, kejelasan bicara, kemampuan menjawab, dan kemampuan menjelaskan.',
                'weight'      => 20.00,
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Kerja Sama Tim',
                'description' => 'Menilai pembagian tugas, keterlibatan anggota, dan koordinasi tim.',
                'weight'      => 15.00,
                'sort_order'  => 4,
            ],
            [
                'name'        => 'Kreativitas Presentasi',
                'description' => 'Menilai cara menyampaikan, penggunaan poster, media pendukung, dan kreativitas presentasi.',
                'weight'      => 10.00,
                'sort_order'  => 5,
            ],
        ];

        foreach ($scienceCriteria as $criteria) {
            ScfAssessmentCriterion::create([
                'program_id'      => null,
                'assessment_type' => 'science',
                'name'            => $criteria['name'],
                'description'     => $criteria['description'],
                'weight'          => $criteria['weight'],
                'max_score'       => 100,
                'sort_order'      => $criteria['sort_order'],
                'is_active'       => true,
            ]);
        }
        $this->command->info('✓ Science criteria seeded (5 kriteria, total bobot 100%)');

        // ── FOOD CRITERIA (Juri — Penilaian Makanan) ─────────────────────
        // Total weight: 30 + 20 + 25 + 15 + 10 = 100
        $foodCriteria = [
            [
                'name'        => 'Keseimbangan Gizi',
                'description' => '4=Menu lengkap (karbohidrat, protein, sayur/buah, lemak sehat). 3=Menu cukup lengkap, hanya ada 1 komponen kurang. 2=Menu hanya memiliki 2-3 komponen gizi. 1=Menu tidak seimbang, hanya dominan 1 jenis zat gizi.',
                'weight'      => 30.00,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Kreativitas Menu',
                'description' => '4=Inovatif, unik, menarik, ada nilai tambah. 3=Menarik dengan variasi sederhana. 2=Kurang variasi, standar saja. 1=Monoton, tidak ada kreativitas.',
                'weight'      => 20.00,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Rasa & Higienitas',
                'description' => '4=Sangat enak, higienis, layak konsumsi. 3=Enak dan cukup bersih. 2=Rasa biasa, agak kurang higienis. 1=Tidak enak/tidak layak konsumsi.',
                'weight'      => 25.00,
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Penyajian & Estetika',
                'description' => '4=Sangat rapi, menarik, sesuai tema booth. 3=Cukup rapi dan menarik. 2=Kurang rapi. 1=Berantakan.',
                'weight'      => 15.00,
                'sort_order'  => 4,
            ],
            [
                'name'        => 'Penjelasan Menu',
                'description' => '4=Menjelaskan bahan, proses, dan manfaat dengan lancar. 3=Menjelaskan sebagian dengan cukup jelas. 2=Penjelasan terbatas. 1=Tidak bisa menjelaskan.',
                'weight'      => 10.00,
                'sort_order'  => 5,
            ],
        ];

        foreach ($foodCriteria as $criteria) {
            ScfAssessmentCriterion::create([
                'program_id'      => null,
                'assessment_type' => 'food',
                'name'            => $criteria['name'],
                'description'     => $criteria['description'],
                'weight'          => $criteria['weight'],
                'max_score'       => 100,
                'sort_order'      => $criteria['sort_order'],
                'is_active'       => true,
            ]);
        }
        $this->command->info('✓ Food criteria seeded (5 kriteria, total bobot 100%)');

        // ── POSTER CRITERIA (Guru — Penilaian Poster) ────────────────────
        // Kriteria poster configurable, ini adalah default awal
        // Operator dapat mengubah via UI
        $posterCriteria = [
            [
                'name'        => 'Desain & Estetika',
                'description' => 'Tata letak, warna, dan daya tarik visual poster secara keseluruhan.',
                'weight'      => 30.00,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Kelengkapan Informasi',
                'description' => 'Kejelasan dan kelengkapan informasi produk yang disampaikan melalui poster.',
                'weight'      => 30.00,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Relevansi dengan Tema',
                'description' => 'Kesesuaian konten poster dengan tema Science Food Festival.',
                'weight'      => 20.00,
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Kreativitas & Orisinalitas',
                'description' => 'Keunikan ide, kreativitas penyajian, dan orisinalitas desain.',
                'weight'      => 20.00,
                'sort_order'  => 4,
            ],
        ];

        foreach ($posterCriteria as $criteria) {
            ScfAssessmentCriterion::create([
                'program_id'      => null,
                'assessment_type' => 'poster',
                'name'            => $criteria['name'],
                'description'     => $criteria['description'],
                'weight'          => $criteria['weight'],
                'max_score'       => 100,
                'sort_order'      => $criteria['sort_order'],
                'is_active'       => true,
            ]);
        }
        $this->command->info('✓ Poster criteria seeded (4 kriteria, total bobot 100%)');

        // ─────────────────────────────────
        // 5. Assignment Guru SCF
        // ─────────────────────────────────
        $guruAssignments = [
            ['user_id' => 67, 'type' => 'guru'],  // Ibnu Sina Sudrajat
            ['user_id' => 70, 'type' => 'guru'],  // Budi Susanto
        ];

        foreach ($guruAssignments as $ga) {
            $user = User::find($ga['user_id']);
            if ($user && $user->getRoleName() === 'guru') {
                ScfAssignment::firstOrCreate(
                    ['program_id' => $program->id, 'user_id' => $ga['user_id']],
                    ['assignment_type' => $ga['type'], 'status' => 'active']
                );
                $this->command->info("✓ Assigned guru: {$user->getDisplayName()} (ID:{$ga['user_id']}) as {$ga['type']}");
            } else {
                $this->command->warn("⚠ Skipped user ID {$ga['user_id']} — tidak ditemukan atau bukan guru.");
            }
        }

        // ─────────────────────────────────
        // 6. Assignment Juri
        // ─────────────────────────────────
        $juriAssignments = [
            ['user_id' => 73, 'type' => 'juri'],  // Annisa — guru yang jadi juri
        ];

        foreach ($juriAssignments as $ja) {
            $user = User::find($ja['user_id']);
            if ($user && $user->getRoleName() === 'guru') {
                ScfAssignment::firstOrCreate(
                    ['program_id' => $program->id, 'user_id' => $ja['user_id']],
                    ['assignment_type' => $ja['type'], 'status' => 'active']
                );
                $this->command->info("✓ Assigned juri: {$user->getDisplayName()} (ID:{$ja['user_id']})");
            } else {
                $this->command->warn("⚠ Skipped user ID {$ja['user_id']} — tidak ditemukan atau bukan guru.");
            }
        }

        $this->command->info('');
        $this->command->info('=== SCF Seeder selesai ===');
        $this->command->info("Program ID: {$program->id}");
        $this->command->info('');
        $this->command->info('CATATAN:');
        $this->command->info('  - Bobot komponen nilai (poster/science/food) BELUM dikonfigurasi.');
        $this->command->info('  - Contribution factor REDUCED dan NONE BELUM dikonfigurasi.');
        $this->command->info('  - Konfigurasi dilakukan oleh Operator via UI.');
    }
}
