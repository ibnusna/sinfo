<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroup;
use App\Models\ScfGroupMember;
use App\Models\ScfActivityLog;
use App\Models\ScfSetting;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();
        $systemStatus = $program ? ScfSetting::getSystemStatus($program->id) : 'closed';

        $groups = collect();
        if ($program) {
            $groups = ScfGroup::where('program_id', $program->id)
                ->where('created_by', $user->id)
                ->with('members', 'poster')
                ->get()
                ->map(function ($group) {
                    $group->leader_data = $group->getLeader();
                    $group->member_count = $group->members->count();
                    $group->members_data = $group->members->map(function ($member) {
                        $student = $member->getStudent();
                        $siswa = $member->getSiswaDetail();
                        return [
                            'user_id' => $member->student_user_id,
                            'nama' => $student?->getDisplayName() ?? 'Unknown',
                            'nis' => $siswa?->nis ?? '',
                            'kelas' => $siswa?->kelas?->nama_kelas ?? 'Tanpa Kelas'
                        ];
                    })->values()->all();
                    return $group;
                });
        }

        // Semua siswa dari auth_gara untuk pilihan anggota
        $allStudents = Siswa::with('kelas')->orderBy('nama')->get();

        // Ambil kelas unik terurut untuk filter
        $classes = $allStudents->map(function ($s) {
            return $s->kelas->nama_kelas ?? null;
        })->filter()->unique()->sort()->values()->all();

        // Siapkan array sederhana siswa untuk JS di blade
        $allStudentsData = $allStudents->map(function($s) {
            return [
                'user_id' => $s->user_id,
                'nama' => $s->nama,
                'nis' => $s->nis,
                'kelas' => $s->kelas->nama_kelas ?? 'Tanpa Kelas'
            ];
        })->values()->all();

        return view('guru.groups.index', compact('program', 'groups', 'allStudents', 'systemStatus', 'classes', 'allStudentsData'));
    }

    /**
     * Simpan kelompok baru + anggota + ketua secara atomic.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'leader_user_id' => ['required', 'integer'],
            'member_ids'     => ['required', 'array', 'min:1'],
            'member_ids.*'   => ['integer'],
        ], [
            'name.required'           => 'Nama kelompok wajib diisi.',
            'leader_user_id.required' => 'Ketua kelompok wajib dipilih.',
            'member_ids.required'     => 'Minimal satu anggota wajib dipilih.',
        ]);

        $program = ScfProgram::getActive();
        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        // Validasi: leader harus siswa
        $leaderUser = User::find($request->leader_user_id);
        if (!$leaderUser || $leaderUser->getRoleName() !== 'siswa') {
            return back()->with('error', 'Ketua kelompok harus merupakan siswa.');
        }

        // Leader harus ada di daftar anggota
        $memberIds = array_unique($request->member_ids);
        if (!in_array($request->leader_user_id, $memberIds)) {
            $memberIds[] = $request->leader_user_id;
        }

        // Validasi: tidak ada siswa yang sudah terdaftar di kelompok lain
        $existingMemberUserIds = ScfGroupMember::whereHas('group', function ($q) use ($program) {
            $q->where('program_id', $program->id);
        })->whereIn('student_user_id', $memberIds)->pluck('student_user_id')->toArray();

        if (!empty($existingMemberUserIds)) {
            $names = collect($existingMemberUserIds)->map(function ($id) {
                $user = User::find($id);
                return $user?->getDisplayName() ?? "ID:{$id}";
            })->join(', ');

            return back()->with('error', "Siswa berikut sudah terdaftar di kelompok lain: {$names}");
        }

        try {
            DB::connection('scf')->transaction(function () use ($request, $program, $authUser, $memberIds) {
                $group = ScfGroup::create([
                    'program_id'     => $program->id,
                    'name'           => $request->name,
                    'leader_user_id' => $request->leader_user_id,
                    'created_by'     => $authUser->id,
                ]);

                foreach ($memberIds as $userId) {
                    ScfGroupMember::create([
                        'group_id'        => $group->id,
                        'student_user_id' => $userId,
                        'created_at'      => now(),
                    ]);
                }

                $leaderName = User::find($request->leader_user_id)?->getDisplayName();

                ScfActivityLog::log(
                    $authUser->id,
                    'group_created',
                    "Guru membuat kelompok '{$request->name}' dengan ketua {$leaderName}.",
                    $program->id
                );
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat membuat kelompok. Coba lagi.');
        }

        return back()->with('success', "Kelompok '{$request->name}' berhasil dibuat.");
    }

    /**
     * Update kelompok + anggota + ketua secara atomic.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'leader_user_id' => ['required', 'integer'],
            'member_ids'     => ['required', 'array', 'min:1'],
            'member_ids.*'   => ['integer'],
        ], [
            'name.required'           => 'Nama kelompok wajib diisi.',
            'leader_user_id.required' => 'Ketua kelompok wajib dipilih.',
            'member_ids.required'     => 'Minimal satu anggota wajib dipilih.',
        ]);

        $group = ScfGroup::findOrFail($id);

        if ($group->created_by !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengedit kelompok ini.');
        }

        if ($group->isFinalized()) {
            return back()->with('error', 'Kelompok ini sudah difinalisasi dan tidak dapat diedit.');
        }

        $program = ScfProgram::getActive();
        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        $leaderUser = User::find($request->leader_user_id);
        if (!$leaderUser || $leaderUser->getRoleName() !== 'siswa') {
            return back()->with('error', 'Ketua kelompok harus merupakan siswa.');
        }

        $memberIds = array_unique($request->member_ids);
        if (!in_array($request->leader_user_id, $memberIds)) {
            $memberIds[] = $request->leader_user_id;
        }

        // Validasi: siswa tidak boleh terdaftar di kelompok lain (selain kelompok ini)
        $existingMemberUserIds = ScfGroupMember::whereHas('group', function ($q) use ($program, $id) {
            $q->where('program_id', $program->id)->where('id', '!=', $id);
        })->whereIn('student_user_id', $memberIds)->pluck('student_user_id')->toArray();

        if (!empty($existingMemberUserIds)) {
            $names = collect($existingMemberUserIds)->map(function ($id) {
                $user = User::find($id);
                return $user?->getDisplayName() ?? "ID:{$id}";
            })->join(', ');

            return back()->with('error', "Siswa berikut sudah terdaftar di kelompok lain: {$names}");
        }

        try {
            DB::connection('scf')->transaction(function () use ($request, $group, $memberIds, $program) {
                $group->update([
                    'name'           => $request->name,
                    'leader_user_id' => $request->leader_user_id,
                ]);

                // Sync members: delete old, create new
                ScfGroupMember::where('group_id', $group->id)->delete();

                foreach ($memberIds as $userId) {
                    ScfGroupMember::create([
                        'group_id'        => $group->id,
                        'student_user_id' => $userId,
                        'created_at'      => now(),
                    ]);
                }

                $leaderName = User::find($request->leader_user_id)?->getDisplayName();

                ScfActivityLog::log(
                    Auth::id(),
                    'group_updated',
                    "Guru mengedit kelompok '{$group->name}' dengan ketua {$leaderName}.",
                    $program->id
                );
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengupdate kelompok.');
        }

        return back()->with('success', "Kelompok '{$request->name}' berhasil diupdate.");
    }

    public function destroy(int $id)
    {
        $group = ScfGroup::findOrFail($id);

        // Hanya guru yang membuat yang bisa menghapus
        if ($group->created_by !== Auth::id()) {
            abort(403, 'Anda tidak berhak menghapus kelompok ini.');
        }

        DB::connection('scf')->transaction(function () use ($group) {
            ScfGroupMember::where('group_id', $group->id)->delete();
            $group->delete();
        });

        return back()->with('success', "Kelompok berhasil dihapus.");
    }
}
