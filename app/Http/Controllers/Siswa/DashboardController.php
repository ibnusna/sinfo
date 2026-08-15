<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroupMember;
use App\Models\ScfDocument;
use App\Models\ScfSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();
        $systemStatus = $program ? ScfSetting::getSystemStatus($program->id) : 'closed';

        $myGroup = null;
        $myPoster = null;
        $isLeader = false;
        $documents = collect();

        if ($program) {
            $membership = ScfGroupMember::where('student_user_id', $user->id)
                ->whereHas('group', function ($q) use ($program) {
                    $q->where('program_id', $program->id);
                })
                ->with('group.members', 'group.poster', 'group.productMetadata')
                ->first();

            if ($membership) {
                $myGroup = $membership->group;
                $isLeader = $myGroup->isLeader($user->id);
                $myPoster = $myGroup->poster;
            }

            $documents = ScfDocument::where('program_id', $program->id)
                ->where('status', 'published')
                ->get();
        }

        return view('siswa.dashboard', compact(
            'program', 'systemStatus', 'myGroup', 'myPoster', 'isLeader', 'documents', 'user'
        ));
    }
}
