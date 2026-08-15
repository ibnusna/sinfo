<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfAssessment;
use App\Models\ScfAssessmentCriterion;
use App\Models\ScfAssessmentDetail;
use App\Models\ScfGroup;
use App\Models\ScfActivityLog;
use App\Services\ScfScoreCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function __construct(
        private ScfScoreCalculationService $scoreService
    ) {}

    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();

        $criteria = ScfAssessmentCriterion::getForType('science', $program?->id);

        $groups = collect();
        if ($program) {
            $groups = ScfGroup::where('program_id', $program->id)
                ->where('created_by', $user->id)
                ->with(['poster', 'members', 'productMetadata'])
                ->get()
                ->map(function ($group) use ($user) {
                    $group->my_assessment = ScfAssessment::where('group_id', $group->id)
                        ->where('assessor_user_id', $user->id)
                        ->where('assessment_type', 'science')
                        ->with('details')
                        ->first();

                    $group->poster_assessment = ScfAssessment::where('group_id', $group->id)
                        ->where('assessor_user_id', $user->id)
                        ->where('assessment_type', 'poster')
                        ->with('details')
                        ->first();

                    $group->juri_assessments = ScfAssessment::where('group_id', $group->id)
                        ->where('assessment_type', 'food')
                        ->where('status', 'submitted')
                        ->with('details')
                        ->get();

                    return $group;
                });
        }

        $posterCriteria = ScfAssessmentCriterion::getForType('poster', $program?->id);

        return view('guru.assessments.index', compact('program', 'groups', 'criteria', 'posterCriteria'));
    }

    /**
     * Simpan penilaian IPA (science) atau poster.
     * assessment_type dikirim dari form: 'science' atau 'poster'
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_id'        => ['required', 'integer'],
            'assessment_type' => ['required', 'in:science,poster'],
            'scores'          => ['required', 'array'],
            'scores.*'        => ['numeric', 'min:0', 'max:100'],
            'notes'           => ['array'],
            'notes.*'         => ['nullable', 'string', 'max:500'],
            'action'          => ['required', 'in:draft,submit'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();
        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        $group = ScfGroup::findOrFail($request->group_id);

        // Pastikan guru yang menilai adalah yang membuat kelompok
        if ($group->created_by !== $user->id) {
            abort(403, 'Anda tidak berhak menilai kelompok ini.');
        }

        $assessmentType = $request->assessment_type;

        // Cek apakah sudah ada assessment yang submitted (tidak bisa diedit)
        $existing = ScfAssessment::where('group_id', $group->id)
            ->where('assessor_user_id', $user->id)
            ->where('assessment_type', $assessmentType)
            ->first();

        if ($existing && $existing->isSubmitted()) {
            $typeLabel = $assessmentType === 'science' ? 'IPA' : 'Poster';
            return back()->with('error', "Penilaian {$typeLabel} sudah disubmit dan tidak dapat diubah.");
        }

        $isSubmit = $request->action === 'submit';

        try {
            DB::connection('scf')->transaction(function () use ($request, $user, $program, $group, $existing, $isSubmit, $assessmentType) {
                $assessment = $existing ?? ScfAssessment::create([
                    'program_id'       => $program->id,
                    'group_id'         => $group->id,
                    'assessor_user_id' => $user->id,
                    'assessor_type'    => 'guru',
                    'assessment_type'  => $assessmentType,
                    'status'           => 'draft',
                ]);

                // Hapus detail lama
                ScfAssessmentDetail::where('assessment_id', $assessment->id)->delete();

                foreach ($request->scores as $criterion => $score) {
                    ScfAssessmentDetail::create([
                        'assessment_id' => $assessment->id,
                        'criterion'     => $criterion,
                        'score'         => $score,
                        'note'          => $request->notes[$criterion] ?? null,
                    ]);
                }

                if ($isSubmit) {
                    $assessment->update([
                        'status'       => 'submitted',
                        'submitted_at' => now(),
                    ]);

                    $typeLabel = $assessmentType === 'science' ? 'IPA' : 'Poster';
                    ScfActivityLog::log(
                        $user->id,
                        'assessment_submitted',
                        "Guru mengirim penilaian {$typeLabel} untuk kelompok '{$group->name}'.",
                        $program->id
                    );
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan penilaian.');
        }

        $msg = $isSubmit ? 'Penilaian berhasil disubmit.' : 'Draft penilaian berhasil disimpan.';
        return back()->with('success', $msg);
    }
}
