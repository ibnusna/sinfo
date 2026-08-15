<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfAssignment;
use App\Models\ScfGroup;
use App\Models\ScfPoster;
use App\Models\ScfDocument;
use App\Models\ScfActivityLog;
use App\Models\ScfSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $program = ScfProgram::getActive();
        $systemStatus = $program ? ScfSetting::getSystemStatus($program->id) : 'closed';

        $stats = [
            'guru_count' => 0,
            'juri_count' => 0,
            'group_count' => 0,
            'poster_count' => 0,
            'poster_submitted' => 0,
        ];

        $recentLogs = collect();

        if ($program) {
            $stats['guru_count'] = ScfAssignment::where('program_id', $program->id)
                ->where('assignment_type', 'guru')
                ->where('status', 'active')
                ->count();

            $stats['juri_count'] = ScfAssignment::where('program_id', $program->id)
                ->where('assignment_type', 'juri')
                ->where('status', 'active')
                ->count();

            $stats['group_count'] = ScfGroup::where('program_id', $program->id)->count();

            $stats['poster_count'] = ScfPoster::where('program_id', $program->id)->count();

            $stats['poster_submitted'] = ScfPoster::where('program_id', $program->id)
                ->whereIn('status', ['submitted', 'approved'])
                ->count();

            $recentLogs = ScfActivityLog::where('program_id', $program->id)
                ->latest('created_at')
                ->limit(10)
                ->get();
        }

        return view('operator.dashboard', compact(
            'program', 'systemStatus', 'stats', 'recentLogs'
        ));
    }

    /**
     * Buka / tutup sistem SCF.
     */
    public function toggleSystem(Request $request)
    {
        $request->validate([
            'status' => ['required', 'in:open,closed'],
        ]);

        $program = ScfProgram::getActive();
        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        ScfSetting::setSystemStatus($request->status, $program->id);

        $statusLabel = $request->status === 'open' ? 'DIBUKA' : 'DITUTUP';

        ScfActivityLog::log(
            Auth::id(),
            'system_toggle',
            "Operator {$statusLabel} sistem Science Food Festival.",
            $program->id
        );

        return back()->with('success', "Sistem Science Food Festival berhasil {$statusLabel}.");
    }
}
