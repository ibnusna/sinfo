<?php

namespace App\Http\Controllers;

use App\Models\ScfProgram;
use App\Models\ScfSetting;
use App\Models\ScfDocument;
use App\Models\ScfGroup;
use App\Models\ScfAssessment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class KepsekController extends Controller
{
    public function dashboard(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();
        $systemStatus = $program ? ScfSetting::getSystemStatus($program->id) : 'closed';

        $stats = ['group_count' => 0, 'poster_submitted' => 0, 'assessed' => 0];
        $groups = collect();

        if ($program) {
            $stats['group_count'] = ScfGroup::where('program_id', $program->id)->count();
            $stats['poster_submitted'] = \App\Models\ScfPoster::where('program_id', $program->id)
                ->whereIn('status', ['submitted', 'approved'])->count();
            $stats['assessed'] = ScfAssessment::where('program_id', $program->id)
                ->where('status', 'submitted')->count();
            $groups = ScfGroup::where('program_id', $program->id)
                ->with('poster', 'assessments')->get();
        }

        return view('kepsek.dashboard', compact('program', 'systemStatus', 'stats', 'groups', 'user'));
    }
}
