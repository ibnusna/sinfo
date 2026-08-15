<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroup;
use App\Models\ScfScoreResult;
use App\Models\ScfMemberContribution;
use App\Models\ScfProductMetadata;
use App\Models\ScfAssessment;
use App\Models\User;
use App\Services\ScfScoreCalculationService;
use App\Services\ScfContributionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScoreController extends Controller
{
    public function __construct(
        private ScfScoreCalculationService $scoreService,
        private ScfContributionService $contributionService
    ) {}

    /**
     * Tampilkan rekap nilai satu kelompok.
     *
     * Guru boleh melihat:
     * - Poster score, Science score, Food score, Group score
     * - Individual score per anggota
     * - Contribution reason (PRIVAT — hanya guru)
     *
     * Juri dan siswa TIDAK boleh mengakses halaman ini.
     */
    public function show(int $groupId): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();

        $group = ScfGroup::with(['members', 'poster', 'productMetadata'])->findOrFail($groupId);

        // Pastikan guru yang membuat kelompok ini
        if ($group->created_by !== $user->id) {
            abort(403, 'Anda tidak berhak melihat rekap nilai kelompok ini.');
        }

        // Cek apakah sudah difinalisasi
        $isFinalized = $group->isFinalized();
        $finalScores = collect();

        if ($isFinalized) {
            // Ambil snapshot dari score_results
            $finalScores = ScfScoreResult::forGroup($groupId);
            $finalScores = $finalScores->map(function ($result) {
                $student = User::find($result->student_user_id);
                $result->student_name = $student?->getDisplayName() ?? "ID:{$result->student_user_id}";
                return $result;
            });
        }

        // Hitung nilai real-time (untuk preview sebelum finalisasi)
        $scorePreview = $this->scoreService->recalculate($group);

        // Contribution data (PRIVAT — hanya guru)
        $contributions = $this->contributionService->getContributionsForGuru($group);
        $contributionMap = $contributions->keyBy('student_user_id');

        // Data anggota dengan contribution factor preview
        $memberScores = $group->members->map(function ($member) use ($scorePreview, $contributionMap, $group) {
            $student = User::find($member->student_user_id);
            $contribution = $contributionMap->get($member->student_user_id);

            $factor = null;
            $individualScore = null;

            if ($scorePreview['score'] !== null && $contribution) {
                if ($contribution->is_contributed) {
                    $factor = 1.00;
                } else {
                    // Factor none dari settings (mungkin null jika belum dikonfigurasi)
                    $factorFromSettings = \App\Models\ScfSetting::getValue('contribution_factor_none', $group->program_id);
                    $factor = $factorFromSettings !== null ? (float) $factorFromSettings : null;
                }

                if ($factor !== null) {
                    $individualScore = round($scorePreview['score'] * $factor, 4);
                }
            }

            return (object)[
                'student_user_id' => $member->student_user_id,
                'name'            => $student?->getDisplayName() ?? "ID:{$member->student_user_id}",
                'is_contributed'  => $contribution?->is_contributed ?? null,
                'reason'          => $contribution?->reason ?? null,
                'factor'          => $factor,
                'individual_score'=> $individualScore,
            ];
        });

        // Validasi prasyarat finalisasi
        $finalizationCheck = $this->scoreService->validateFinalizationPrerequisites($group);

        // Rincian assessment per komponen
        $posterAssessment = ScfAssessment::where('group_id', $groupId)
            ->where('assessment_type', 'poster')
            ->where('status', 'submitted')
            ->with('details')
            ->first();

        $scienceAssessment = ScfAssessment::where('group_id', $groupId)
            ->where('assessment_type', 'science')
            ->where('status', 'submitted')
            ->with('details')
            ->first();

        $foodAssessments = ScfAssessment::where('group_id', $groupId)
            ->where('assessment_type', 'food')
            ->where('status', 'submitted')
            ->with('details')
            ->get()
            ->map(function ($assessment) {
                $assessor = $assessment->getAssessor();
                $assessment->assessor_name = $assessor?->getDisplayName() ?? "ID:{$assessment->assessor_user_id}";
                return $assessment;
            });

        return view('guru.scores.show', compact(
            'program', 'group', 'isFinalized', 'finalScores',
            'scorePreview', 'memberScores', 'finalizationCheck',
            'contributions', 'posterAssessment', 'scienceAssessment', 'foodAssessments'
        ));
    }
}
