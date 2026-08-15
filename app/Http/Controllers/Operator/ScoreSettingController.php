<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfSetting;
use App\Models\ScfAssessmentCriterion;
use App\Models\ScfActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ScoreSettingController extends Controller
{
    /**
     * Tampilkan halaman konfigurasi sistem penilaian.
     * Operator dapat mengatur:
     * - Bobot komponen nilai kelompok (poster/science/food)
     * - Contribution factor (full/reduced/none)
     * - Lihat daftar assessment criteria
     */
    public function index(): View
    {
        $program = ScfProgram::getActive();

        $settings = [];
        $criteria = collect();

        if ($program) {
            $settingKeys = [
                'poster_weight', 'science_weight', 'food_weight',
                'contribution_factor_full',
                'contribution_factor_reduced',
                'contribution_factor_none',
            ];

            foreach ($settingKeys as $key) {
                $settings[$key] = ScfSetting::getValue($key, $program->id);
            }

            // Semua criteria per tipe
            $criteria = [
                'poster'  => ScfAssessmentCriterion::getForType('poster', $program->id),
                'science' => ScfAssessmentCriterion::getForType('science', $program->id),
                'food'    => ScfAssessmentCriterion::getForType('food', $program->id),
            ];
        }

        return view('operator.score_settings', compact('program', 'settings', 'criteria'));
    }

    /**
     * Update konfigurasi bobot komponen nilai.
     */
    public function updateWeights(Request $request)
    {
        $request->validate([
            'poster_weight'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'science_weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'food_weight'    => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $program = ScfProgram::getActive();
        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        $posterW  = $request->poster_weight !== null ? (float) $request->poster_weight : null;
        $scienceW = $request->science_weight !== null ? (float) $request->science_weight : null;
        $foodW    = $request->food_weight !== null ? (float) $request->food_weight : null;

        // Validasi: jika ketiganya diisi, harus total = 100
        if ($posterW !== null && $scienceW !== null && $foodW !== null) {
            $total = $posterW + $scienceW + $foodW;
            if (abs($total - 100) > 0.01) {
                return back()->with('error', "Total bobot harus 100%. Saat ini: {$total}%.");
            }
        }

        try {
            DB::connection('scf')->transaction(function () use ($program, $posterW, $scienceW, $foodW) {
                ScfSetting::updateOrCreate(
                    ['program_id' => $program->id, 'key' => 'poster_weight'],
                    ['value' => $posterW]
                );
                ScfSetting::updateOrCreate(
                    ['program_id' => $program->id, 'key' => 'science_weight'],
                    ['value' => $scienceW]
                );
                ScfSetting::updateOrCreate(
                    ['program_id' => $program->id, 'key' => 'food_weight'],
                    ['value' => $foodW]
                );

                ScfActivityLog::log(
                    Auth::id(),
                    'score_weights_updated',
                    "Operator mengupdate bobot nilai: Poster={$posterW}%, IPA={$scienceW}%, Makanan={$foodW}%.",
                    $program->id
                );
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan bobot.');
        }

        return back()->with('success', 'Bobot komponen nilai berhasil disimpan.');
    }

    /**
     * Update konfigurasi contribution factor.
     */
    public function updateContributionFactors(Request $request)
    {
        $request->validate([
            'contribution_factor_full'    => ['nullable', 'numeric', 'min:0', 'max:1'],
            'contribution_factor_reduced' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'contribution_factor_none'    => ['nullable', 'numeric', 'min:0', 'max:1'],
        ]);

        $program = ScfProgram::getActive();
        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        try {
            DB::connection('scf')->transaction(function () use ($request, $program) {
                $keys = ['contribution_factor_full', 'contribution_factor_reduced', 'contribution_factor_none'];
                foreach ($keys as $key) {
                    $value = $request->$key !== null ? (float) $request->$key : null;
                    ScfSetting::updateOrCreate(
                        ['program_id' => $program->id, 'key' => $key],
                        ['value' => $value]
                    );
                }

                ScfActivityLog::log(
                    Auth::id(),
                    'contribution_factors_updated',
                    "Operator mengupdate contribution factor: Full={$request->contribution_factor_full}, Reduced={$request->contribution_factor_reduced}, None={$request->contribution_factor_none}.",
                    $program->id
                );
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan contribution factor.');
        }

        return back()->with('success', 'Contribution factor berhasil disimpan.');
    }

    /**
     * Update assessment criteria (nama, deskripsi, bobot).
     */
    public function updateCriterion(Request $request, int $criterionId)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'weight'      => ['required', 'numeric', 'min:0', 'max:100'],
            'max_score'   => ['required', 'numeric', 'min:1', 'max:100'],
            'is_active'   => ['boolean'],
        ]);

        $criterion = ScfAssessmentCriterion::findOrFail($criterionId);

        $criterion->update([
            'name'        => $request->name,
            'description' => $request->description,
            'weight'      => $request->weight,
            'max_score'   => $request->max_score,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Kriteria '{$criterion->name}' berhasil diupdate.");
    }
}
