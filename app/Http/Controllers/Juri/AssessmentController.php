<?php

namespace App\Http\Controllers\Juri;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroup;
use App\Models\ScfAssessment;
use App\Models\ScfAssessmentCriterion;
use App\Models\ScfAssessmentDetail;
use App\Models\ScfActivityLog;
use App\Models\ScfProductMetadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();

        $criteria = ScfAssessmentCriterion::getForType('food', $program?->id);

        $groups = collect();
        if ($program) {
            $groups = ScfGroup::where('program_id', $program->id)
                ->with(['poster'])
                ->get()
                ->map(function ($group) use ($user) {
                    $group->my_assessment = ScfAssessment::where('group_id', $group->id)
                        ->where('assessor_user_id', $user->id)
                        ->where('assessment_type', 'food')
                        ->with('details')
                        ->first();

                    $group->leader_data = \App\Models\User::with('siswa.kelas')->find($group->leader_user_id);
                    return $group;
                });
        }

        // Ambil list kelas unik terurut dari kelompok juri
        $classes = $groups->map(function ($g) {
            return $g->leader_data?->siswa?->kelas?->nama_kelas ?? null;
        })->filter()->unique()->sort()->values()->all();

        return view('juri.assessments.index', compact('program', 'groups', 'criteria', 'classes'));
    }

    public function show(int $groupId): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();

        $group = ScfGroup::with(['poster', 'members'])->findOrFail($groupId);

        $assessment = ScfAssessment::where('group_id', $groupId)
            ->where('assessor_user_id', $user->id)
            ->where('assessment_type', 'food')
            ->with('details')
            ->first();

        $group->leader_data = $group->getLeader();

        // Juri boleh melihat metadata produk sebagai referensi
        $productMetadata = ScfProductMetadata::forGroup($groupId);

        $criteria = ScfAssessmentCriterion::getForType('food', $program?->id);

        // TIDAK mengirim data contribution ke juri
        return view('juri.assessments.show', compact(
            'program', 'group', 'assessment', 'productMetadata', 'criteria'
        ));
    }

    public function store(Request $request, int $groupId)
    {
        $request->validate([
            'scores'   => ['required', 'array'],
            'scores.*' => ['numeric', 'min:0', 'max:100'],
            'notes'    => ['array'],
            'notes.*'  => ['nullable', 'string', 'max:500'],
            'action'   => ['required', 'in:draft,submit'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();
        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        $group = ScfGroup::findOrFail($groupId);

        // Cek apakah sudah submitted — LOCKED
        $existing = ScfAssessment::where('group_id', $groupId)
            ->where('assessor_user_id', $user->id)
            ->where('assessment_type', 'food')
            ->first();

        if ($existing && $existing->isSubmitted()) {
            return back()->with('error', 'Penilaian sudah disubmit dan terkunci. Tidak dapat diubah.');
        }

        $isSubmit = $request->action === 'submit';

        try {
            DB::connection('scf')->transaction(function () use ($request, $user, $program, $group, $existing, $groupId, $isSubmit) {
                if ($existing) {
                    ScfAssessmentDetail::where('assessment_id', $existing->id)->delete();
                    $assessment = $existing;
                } else {
                    $assessment = ScfAssessment::create([
                        'program_id'       => $program->id,
                        'group_id'         => $groupId,
                        'assessor_user_id' => $user->id,
                        'assessor_type'    => 'juri',
                        'assessment_type'  => 'food',
                        'status'           => 'draft',
                    ]);
                }

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

                    ScfActivityLog::log(
                        $user->id,
                        'juri_assessment_submitted',
                        "Juri {$user->getDisplayName()} mengirim penilaian makanan untuk kelompok '{$group->name}'.",
                        $program->id
                    );
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan penilaian.');
        }

        $msg = $isSubmit ? 'Penilaian berhasil disubmit dan terkunci.' : 'Draft penilaian tersimpan.';
        return redirect()->route('juri.assessments.index')->with('success', $msg);
    }
}
