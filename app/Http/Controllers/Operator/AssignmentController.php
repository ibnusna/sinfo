<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfAssignment;
use App\Models\ScfActivityLog;
use App\Models\User;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(): View
    {
        $program = ScfProgram::getActive();

        $assignments = collect();
        $availableGurus = collect();

        if ($program) {
            $assignments = ScfAssignment::where('program_id', $program->id)
                ->where('status', 'active')
                ->get()
                ->map(function ($assignment) {
                    $assignment->user = $assignment->getUser();
                    return $assignment;
                });

            // Guru yang tersedia (role_id = 4 = guru, belum di-assign)
            $assignedUserIds = ScfAssignment::where('program_id', $program->id)
                ->where('status', 'active')
                ->pluck('user_id')
                ->toArray();

            $availableGurus = User::where('role_id', 4)
                ->where('status_aktif', 1)
                ->whereNotIn('id', $assignedUserIds)
                ->with('guru')
                ->get();
        }

        return view('operator.assignments', compact('program', 'assignments', 'availableGurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'         => ['required', 'integer'],
            'assignment_type' => ['required', 'in:guru,juri'],
        ], [
            'user_id.required'         => 'Pilih guru yang akan di-assign.',
            'assignment_type.required' => 'Tentukan jenis assignment (Guru / Juri).',
        ]);

        $program = ScfProgram::getActive();
        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        // Validasi: user harus ada dan harus guru di auth_gara
        $user = User::find($request->user_id);
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        if ($user->getRoleName() !== 'guru') {
            return back()->with('error', 'Hanya guru yang dapat di-assign ke SCF.');
        }

        // Cek apakah sudah ada assignment untuk program ini
        $exists = ScfAssignment::where('program_id', $program->id)
            ->where('user_id', $request->user_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Guru ini sudah memiliki assignment di program ini.');
        }

        ScfAssignment::create([
            'program_id'      => $program->id,
            'user_id'         => $request->user_id,
            'assignment_type' => $request->assignment_type,
            'status'          => 'active',
        ]);

        $typeLabel = $request->assignment_type === 'juri' ? 'Juri' : 'Guru SCF';

        ScfActivityLog::log(
            Auth::id(),
            'assignment_created',
            "Operator menambahkan {$user->getDisplayName()} sebagai {$typeLabel}.",
            $program->id
        );

        return back()->with('success', "{$user->getDisplayName()} berhasil di-assign sebagai {$typeLabel}.");
    }

    public function destroy(int $id)
    {
        $assignment = ScfAssignment::findOrFail($id);
        $program = ScfProgram::getActive();

        $user = $assignment->getUser();
        $typeLabel = $assignment->assignment_type === 'juri' ? 'Juri' : 'Guru SCF';

        $assignment->update(['status' => 'inactive']);

        ScfActivityLog::log(
            Auth::id(),
            'assignment_removed',
            "Operator menghapus assignment {$user?->getDisplayName()} sebagai {$typeLabel}.",
            $program?->id
        );

        return back()->with('success', "Assignment berhasil dihapus.");
    }
}
