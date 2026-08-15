<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroup;
use App\Models\ScfGroupMember;
use App\Models\ScfMemberContribution;
use App\Services\ScfContributionService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContributionController extends Controller
{
    public function __construct(
        private ScfContributionService $contributionService
    ) {}

    /**
     * Tampilkan form kontribusi.
     * Hanya ketua kelompok yang boleh mengisi.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();

        $group = null;
        $isLeader = false;
        $contributions = collect();
        $members = collect();
        $alreadySubmitted = false;

        if ($program) {
            $member = ScfGroupMember::where('student_user_id', $user->id)
                ->whereHas('group', fn($q) => $q->where('program_id', $program->id))
                ->first();

            if ($member) {
                $group = ScfGroup::with('members')->find($member->group_id);
                $isLeader = $group?->isLeader($user->id) ?? false;

                if ($group) {
                    $alreadySubmitted = ScfMemberContribution::isSubmittedForGroup($group->id);
                    $contributions = ScfMemberContribution::forGroup($group->id);

                    // Ambil data siswa untuk setiap anggota
                    $members = $group->members->map(function ($m) use ($contributions) {
                        $student = User::find($m->student_user_id);
                        $contribution = $contributions->firstWhere('student_user_id', $m->student_user_id);
                        return (object)[
                            'student_user_id' => $m->student_user_id,
                            'name'            => $student?->getDisplayName() ?? "ID:{$m->student_user_id}",
                            'is_contributed'  => $contribution?->is_contributed ?? true,
                            'reason'          => $contribution?->reason ?? null,
                        ];
                    });
                }
            }
        }

        return view('siswa.contribution', compact(
            'program', 'group', 'isLeader', 'members', 'alreadySubmitted', 'contributions'
        ));
    }

    /**
     * Simpan data kontribusi anggota.
     */
    public function store(Request $request)
    {
        $request->validate([
            'all_contributed'        => ['required', 'boolean'],
            'members'                => ['array'],
            'members.*.student_user_id' => ['integer'],
            'members.*.is_contributed'  => ['boolean'],
            'members.*.reason'          => ['nullable', 'string', 'max:1000'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();

        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        $member = ScfGroupMember::where('student_user_id', $user->id)
            ->whereHas('group', fn($q) => $q->where('program_id', $program->id))
            ->first();

        if (!$member) {
            return back()->with('error', 'Anda tidak terdaftar di kelompok manapun.');
        }

        $group = ScfGroup::find($member->group_id);

        $result = $this->contributionService->submitContributions($group, [
            'all_contributed' => (bool) $request->all_contributed,
            'members'         => $request->members ?? [],
        ], $user);

        if (!$result['ok']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }
}
