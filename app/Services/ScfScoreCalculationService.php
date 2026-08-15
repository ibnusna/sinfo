<?php

namespace App\Services;

use App\Models\ScfGroup;
use App\Models\ScfAssessment;
use App\Models\ScfAssessmentCriterion;
use App\Models\ScfMemberContribution;
use App\Models\ScfScoreResult;
use App\Models\ScfSetting;
use App\Models\ScfActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * ScfScoreCalculationService
 *
 * Satu-satunya tempat logika kalkulasi nilai SINFO.
 * Controller HANYA boleh memanggil service ini — tidak boleh menghitung sendiri.
 *
 * Alur kalkulasi:
 * POSTER + SCIENCE + FOOD → GROUP SCORE → × contribution_factor → INDIVIDUAL SCORE
 *
 * PRINSIP WAJIB:
 * - group_score TIDAK PERNAH berubah karena contribution_factor.
 * - individual_score = group_score × contribution_factor.
 * - Nilai tidak boleh berubah setelah status = finalized.
 * - Semua bobot berasal dari scf_settings (configurable oleh operator).
 */
class ScfScoreCalculationService
{
    // ─────────────────────────────────────────────────────────
    // KOMPONEN 1: POSTER SCORE
    // ─────────────────────────────────────────────────────────

    /**
     * Hitung poster_score untuk satu kelompok.
     *
     * Poster dinilai oleh guru yang membuat kelompok (assessor_type=guru, assessment_type=poster).
     * Return null jika belum ada assessment poster yang submitted.
     */
    public function calculatePosterScore(ScfGroup $group): ?float
    {
        $assessment = ScfAssessment::where('group_id', $group->id)
            ->where('assessment_type', 'poster')
            ->where('status', 'submitted')
            ->with('details')
            ->first();

        if (!$assessment) {
            return null;
        }

        $score = $assessment->calculateWeightedScore($group->program_id);
        return round($score, 4);
    }

    // ─────────────────────────────────────────────────────────
    // KOMPONEN 2: SCIENCE SCORE (Guru — Penilaian IPA)
    // ─────────────────────────────────────────────────────────

    /**
     * Hitung science_score untuk satu kelompok.
     *
     * Guru memberikan penilaian IPA (assessor_type=guru, assessment_type=science).
     * Return null jika belum submitted.
     */
    public function calculateScienceScore(ScfGroup $group): ?float
    {
        $assessment = ScfAssessment::where('group_id', $group->id)
            ->where('assessment_type', 'science')
            ->where('status', 'submitted')
            ->with('details')
            ->first();

        if (!$assessment) {
            return null;
        }

        $score = $assessment->calculateWeightedScore($group->program_id);
        return round($score, 4);
    }

    // ─────────────────────────────────────────────────────────
    // KOMPONEN 3: FOOD SCORE (Juri — Penilaian Makanan)
    // ─────────────────────────────────────────────────────────

    /**
     * Hitung food_score untuk satu kelompok.
     *
     * food_score = rata-rata dari seluruh penilaian juri yang berstatus submitted.
     * Assessment dengan status draft TIDAK masuk perhitungan.
     * Return null jika belum ada juri yang submitted.
     */
    public function calculateFoodScore(ScfGroup $group): ?float
    {
        $juryAssessments = ScfAssessment::where('group_id', $group->id)
            ->where('assessment_type', 'food')
            ->where('status', 'submitted')
            ->with('details')
            ->get();

        if ($juryAssessments->isEmpty()) {
            return null;
        }

        $totalScore = 0;
        foreach ($juryAssessments as $assessment) {
            $totalScore += $assessment->calculateWeightedScore($group->program_id);
        }

        $average = $totalScore / $juryAssessments->count();
        return round($average, 4);
    }

    // ─────────────────────────────────────────────────────────
    // NILAI KELOMPOK
    // ─────────────────────────────────────────────────────────

    /**
     * Hitung group_score dari ketiga komponen.
     *
     * Bobot (poster_weight, science_weight, food_weight) dibaca dari scf_settings.
     * Jika bobot belum dikonfigurasi (null), gunakan equal weight (33.33...) dan
     * tandai sebagai "weight belum ditentukan".
     *
     * PENTING: group_score TIDAK dipengaruhi oleh contribution_factor.
     *
     * Return [score, weights_configured] atau null jika salah satu komponen belum selesai.
     */
    public function calculateGroupScore(ScfGroup $group): array
    {
        $posterScore  = $this->calculatePosterScore($group);
        $scienceScore = $this->calculateScienceScore($group);
        $foodScore    = $this->calculateFoodScore($group);

        // Semua komponen harus tersedia
        if ($posterScore === null || $scienceScore === null || $foodScore === null) {
            return [
                'score'               => null,
                'poster_score'        => $posterScore,
                'science_score'       => $scienceScore,
                'food_score'          => $foodScore,
                'poster_weight'       => null,
                'science_weight'      => null,
                'food_weight'         => null,
                'weights_configured'  => false,
                'status'              => 'incomplete',
            ];
        }

        $programId = $group->program_id;

        // Baca bobot dari settings
        $posterWeight  = $this->getSettingFloat('poster_weight', $programId);
        $scienceWeight = $this->getSettingFloat('science_weight', $programId);
        $foodWeight    = $this->getSettingFloat('food_weight', $programId);

        $weightsConfigured = ($posterWeight !== null && $scienceWeight !== null && $foodWeight !== null);

        if (!$weightsConfigured) {
            // Equal weight sebagai fallback — TIDAK menjadi aturan permanen
            $posterWeight  = 100 / 3;
            $scienceWeight = 100 / 3;
            $foodWeight    = 100 / 3;
        }

        // group_score = (poster × w1 + science × w2 + food × w3) / 100
        // Bobot dalam %, jumlah harus = 100
        $groupScore = (
            ($posterScore  * $posterWeight) +
            ($scienceScore * $scienceWeight) +
            ($foodScore    * $foodWeight)
        ) / 100;

        return [
            'score'              => round($groupScore, 4),
            'poster_score'       => $posterScore,
            'science_score'      => $scienceScore,
            'food_score'         => $foodScore,
            'poster_weight'      => $posterWeight,
            'science_weight'     => $scienceWeight,
            'food_weight'        => $foodWeight,
            'weights_configured' => $weightsConfigured,
            'status'             => 'calculated',
        ];
    }

    // ─────────────────────────────────────────────────────────
    // CONTRIBUTION FACTOR
    // ─────────────────────────────────────────────────────────

    /**
     * Dapatkan contribution_factor untuk satu anggota.
     *
     * - is_contributed = true → factor = 1.00 (full)
     * - is_contributed = false → baca contribution_factor_none dari settings
     *
     * Jika factor belum dikonfigurasi di settings → return null (TIDAK dikarang).
     * Null berarti "belum bisa dihitung" bukan 0.
     */
    public function getContributionFactor(int $studentUserId, ScfGroup $group): ?float
    {
        $contribution = ScfMemberContribution::where('group_id', $group->id)
            ->where('student_user_id', $studentUserId)
            ->first();

        if (!$contribution) {
            // Belum ada data kontribusi → anggap full (contribution belum disubmit)
            return null;
        }

        if ($contribution->is_contributed) {
            return (float) ScfSetting::getValue('contribution_factor_full', $group->program_id, 1.00);
        }

        // Tidak berkontribusi → baca dari settings
        $noneFactor = $this->getSettingFloat('contribution_factor_none', $group->program_id);
        return $noneFactor; // Bisa null jika belum dikonfigurasi
    }

    // ─────────────────────────────────────────────────────────
    // NILAI INDIVIDU
    // ─────────────────────────────────────────────────────────

    /**
     * Hitung individual_score untuk satu anggota.
     *
     * individual_score = group_score × contribution_factor
     *
     * WAJIB: group_score TIDAK berubah. Yang berubah hanya individual_score.
     */
    public function calculateIndividualScore(float $groupScore, float $factor): float
    {
        return round($groupScore * $factor, 4);
    }

    // ─────────────────────────────────────────────────────────
    // FINALISASI
    // ─────────────────────────────────────────────────────────

    /**
     * Validasi kelengkapan semua prasyarat sebelum finalisasi.
     *
     * Return array ['ok' => bool, 'missing' => array]
     */
    public function validateFinalizationPrerequisites(ScfGroup $group): array
    {
        $missing = [];

        // 1. Poster harus ada
        if (!$group->poster) {
            $missing[] = 'Poster belum diupload.';
        }

        // 2. Metadata produk harus ada
        if (!$group->productMetadata) {
            $missing[] = 'Metadata produk belum diisi oleh ketua.';
        }

        // 3. Penilaian poster harus submitted
        $posterAssessment = ScfAssessment::where('group_id', $group->id)
            ->where('assessment_type', 'poster')
            ->where('status', 'submitted')
            ->exists();
        if (!$posterAssessment) {
            $missing[] = 'Penilaian poster belum selesai.';
        }

        // 4. Penilaian IPA (science) harus submitted
        $scienceAssessment = ScfAssessment::where('group_id', $group->id)
            ->where('assessment_type', 'science')
            ->where('status', 'submitted')
            ->exists();
        if (!$scienceAssessment) {
            $missing[] = 'Penilaian IPA belum disubmit.';
        }

        // 5. Minimal satu juri sudah submitted
        $foodAssessmentCount = ScfAssessment::where('group_id', $group->id)
            ->where('assessment_type', 'food')
            ->where('status', 'submitted')
            ->count();
        if ($foodAssessmentCount === 0) {
            $missing[] = 'Belum ada penilaian juri yang disubmit.';
        }

        // 6. Kontribusi anggota sudah disubmit
        $contributionSubmitted = ScfMemberContribution::isSubmittedForGroup($group->id);
        if (!$contributionSubmitted) {
            $missing[] = 'Data kontribusi anggota belum disubmit oleh ketua.';
        }

        return [
            'ok'      => empty($missing),
            'missing' => $missing,
        ];
    }

    /**
     * Finalisasi nilai kelompok.
     *
     * Proses:
     * 1. Validasi prasyarat.
     * 2. Hitung semua komponen nilai.
     * 3. Simpan snapshot ke score_results per anggota.
     * 4. Set status = finalized.
     * 5. Log aktivitas.
     *
     * Nilai TIDAK bisa berubah setelah finalized.
     * Seluruh proses dalam satu DB transaction.
     */
    public function finalizeScores(ScfGroup $group, User $guru): array
    {
        // Cek apakah sudah finalized
        if (ScfScoreResult::isGroupFinalized($group->id)) {
            return ['ok' => false, 'message' => 'Nilai kelompok ini sudah difinalisasi sebelumnya.'];
        }

        // Validasi prasyarat
        $validation = $this->validateFinalizationPrerequisites($group);
        if (!$validation['ok']) {
            return ['ok' => false, 'message' => implode(' ', $validation['missing']), 'missing' => $validation['missing']];
        }

        // Hitung group score
        $groupData = $this->calculateGroupScore($group);
        if ($groupData['score'] === null) {
            return ['ok' => false, 'message' => 'Nilai kelompok belum dapat dihitung — ada komponen yang belum selesai.'];
        }

        $groupScore = $groupData['score'];

        try {
            DB::connection('scf')->transaction(function () use ($group, $guru, $groupData, $groupScore) {
                $now = now();
                $members = $group->members;

                // Hapus snapshot lama jika ada (sebelum finalized)
                ScfScoreResult::where('group_id', $group->id)->delete();

                foreach ($members as $member) {
                    $studentUserId = $member->student_user_id;

                    // Dapatkan contribution factor
                    $factor = $this->getContributionFactor($studentUserId, $group);

                    // Jika factor null (belum dikonfigurasi), gunakan 1.00 sebagai default sementara
                    // dan tandai sebagai perlu dikonfigurasi
                    if ($factor === null) {
                        // Cek apakah user ini adalah anggota tidak berkontribusi
                        $contribution = ScfMemberContribution::where('group_id', $group->id)
                            ->where('student_user_id', $studentUserId)
                            ->first();

                        if ($contribution && !$contribution->is_contributed) {
                            // Factor none belum dikonfigurasi — set 0 sebagai placeholder
                            $factor = 0;
                        } else {
                            $factor = 1.00;
                        }
                    }

                    $individualScore = $this->calculateIndividualScore($groupScore, $factor);

                    ScfScoreResult::updateOrCreate(
                        ['group_id' => $group->id, 'student_user_id' => $studentUserId],
                        [
                            'program_id'          => $group->program_id,
                            'poster_score'        => $groupData['poster_score'],
                            'science_score'       => $groupData['science_score'],
                            'food_score'          => $groupData['food_score'],
                            'group_score'         => $groupScore,
                            'poster_weight_used'  => $groupData['poster_weight'],
                            'science_weight_used' => $groupData['science_weight'],
                            'food_weight_used'    => $groupData['food_weight'],
                            'contribution_factor' => $factor,
                            'individual_score'    => $individualScore,
                            'group_status'        => 'finalized',
                            'calculated_at'       => $now,
                            'finalized_at'        => $now,
                            'finalized_by'        => $guru->id,
                        ]
                    );
                }

                ScfActivityLog::log(
                    $guru->id,
                    'score_finalized',
                    "Guru memfinalisasi nilai kelompok '{$group->name}'. Group score: {$groupScore}.",
                    $group->program_id
                );
            });

            return ['ok' => true, 'group_score' => $groupScore, 'weights_configured' => $groupData['weights_configured']];

        } catch (\Exception $e) {
            return ['ok' => false, 'message' => 'Terjadi kesalahan saat memfinalisasi nilai: ' . $e->getMessage()];
        }
    }

    /**
     * Hitung ulang nilai kelompok (recalculate) tanpa finalisasi.
     *
     * Nilai yang sudah finalized TIDAK bisa direcalculate.
     * Untuk preview saja — hasilnya tidak disimpan sebagai finalized.
     */
    public function recalculate(ScfGroup $group): array
    {
        if (ScfScoreResult::isGroupFinalized($group->id)) {
            return ['ok' => false, 'message' => 'Nilai sudah difinalisasi dan tidak dapat direcalculate.'];
        }

        return $this->calculateGroupScore($group);
    }

    // ─────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────

    private function getSettingFloat(string $key, ?int $programId): ?float
    {
        $value = ScfSetting::getValue($key, $programId);
        return ($value !== null && $value !== '') ? (float) $value : null;
    }
}
