<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroup;
use App\Models\ScfGroupMember;
use App\Models\ScfPoster;
use App\Models\ScfActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PosterController extends Controller
{
    public function upload(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();

        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        $membership = ScfGroupMember::where('student_user_id', $user->id)
            ->whereHas('group', fn($q) => $q->where('program_id', $program->id))
            ->with('group')
            ->first();

        if (!$membership) {
            abort(403, 'Anda tidak terdaftar dalam kelompok manapun.');
        }

        $group = $membership->group;

        // SECURITY: Hanya ketua kelompok yang dapat upload poster
        if (!$group->isLeader($user->id)) {
            abort(403, 'Hanya ketua kelompok yang dapat mengupload poster.');
        }

        $request->validate([
            'poster' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
        ], [
            'poster.required' => 'File poster wajib dipilih.',
            'poster.max'      => 'Ukuran file maksimal 10MB.',
            'poster.mimes'    => 'Format file harus JPG, PNG, WEBP, atau PDF.',
        ]);

        $file = $request->file('poster');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $safeFilename = Str::uuid() . '.' . $extension;
        $path = "scf/posters/{$program->id}/{$group->id}/{$safeFilename}";

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        // Hapus poster lama jika ada
        $existing = ScfPoster::where('group_id', $group->id)
            ->where('program_id', $program->id)
            ->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->file_path);
            $existing->delete();
        }

        ScfPoster::create([
            'program_id'    => $program->id,
            'group_id'      => $group->id,
            'uploaded_by'   => $user->id,
            'original_name' => $originalName,
            'file_path'     => $path,
            'mime_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'status'        => 'submitted',
            'uploaded_at'   => now(),
        ]);

        ScfActivityLog::log(
            $user->id,
            'poster_uploaded',
            "Ketua kelompok '{$group->name}' mengupload poster: {$originalName}.",
            $program->id
        );

        return back()->with('success', 'Poster berhasil diupload!');
    }
}
