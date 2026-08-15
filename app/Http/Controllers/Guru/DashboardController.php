<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroup;
use App\Models\ScfPoster;
use App\Models\ScfAssessment;
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

        $stats = [
            'group_count'  => 0,
            'poster_count' => 0,
            'assessed'     => 0,
        ];

        $myGroups = collect();
        $recentPosters = collect();

        if ($program) {
            // Kelompok yang dibuat oleh guru ini
            $myGroups = ScfGroup::where('program_id', $program->id)
                ->where('created_by', $user->id)
                ->with('members', 'poster')
                ->get();

            $stats['group_count'] = $myGroups->count();

            $groupIds = $myGroups->pluck('id')->toArray();

            $stats['poster_count'] = ScfPoster::whereIn('group_id', $groupIds)->count();

            $stats['assessed'] = ScfAssessment::whereIn('group_id', $groupIds)
                ->where('assessor_user_id', $user->id)
                ->where('assessor_type', 'guru')
                ->where('status', 'submitted')
                ->count();

            $recentPosters = ScfPoster::whereIn('group_id', $groupIds)
                ->with('group')
                ->latest('uploaded_at')
                ->limit(5)
                ->get();
        }

        // Juklak & Juknis yang published
        $documents = $program
            ? ScfDocument::where('program_id', $program->id)
                ->where('status', 'published')
                ->get()
            : collect();

        return view('guru.dashboard', compact(
            'program', 'systemStatus', 'stats', 'myGroups', 'recentPosters', 'documents', 'user'
        ));
    }
}
