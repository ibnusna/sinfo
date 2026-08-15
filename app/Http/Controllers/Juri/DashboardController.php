<?php

namespace App\Http\Controllers\Juri;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroup;
use App\Models\ScfAssessment;
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

        $stats = [
            'total_groups'   => 0,
            'assessed'       => 0,
            'pending'        => 0,
        ];

        $groups = collect();

        if ($program) {
            $totalGroups = ScfGroup::where('program_id', $program->id)->count();
            $stats['total_groups'] = $totalGroups;

            $assessed = ScfAssessment::where('program_id', $program->id)
                ->where('assessor_user_id', $user->id)
                ->where('assessor_type', 'juri')
                ->where('status', 'submitted')
                ->count();

            $stats['assessed'] = $assessed;
            $stats['pending'] = $totalGroups - $assessed;

            $groups = ScfGroup::where('program_id', $program->id)
                ->with('poster')
                ->get()
                ->map(function ($group) use ($user) {
                    $group->my_assessment = ScfAssessment::where('group_id', $group->id)
                        ->where('assessor_user_id', $user->id)
                        ->where('assessor_type', 'juri')
                        ->first();
                    $group->leader_data = $group->getLeader();
                    return $group;
                });
        }

        return view('juri.dashboard', compact('program', 'systemStatus', 'stats', 'groups', 'user'));
    }
}
