<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroup;
use App\Models\ScfGroupMember;
use App\Models\ScfProductMetadata;
use App\Models\ScfActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductMetadataController extends Controller
{
    /**
     * Tampilkan form metadata produk.
     * Hanya ketua kelompok yang boleh mengisi.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();

        $group = null;
        $metadata = null;
        $isLeader = false;

        if ($program) {
            // Cari kelompok user ini
            $member = ScfGroupMember::where('student_user_id', $user->id)
                ->whereHas('group', fn($q) => $q->where('program_id', $program->id))
                ->first();

            if ($member) {
                $group = ScfGroup::with(['members', 'poster'])->find($member->group_id);
                $isLeader = $group?->isLeader($user->id) ?? false;
                $metadata = ScfProductMetadata::forGroup($group?->id);
            }
        }

        return view('siswa.product_metadata', compact('program', 'group', 'metadata', 'isLeader'));
    }

    /**
     * Simpan atau update metadata produk.
     * Hanya ketua kelompok yang boleh.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name'   => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'ingredients'    => ['nullable', 'string'],
            'carbohydrate'   => ['nullable', 'string', 'max:500'],
            'protein'        => ['nullable', 'string', 'max:500'],
            'fat'            => ['nullable', 'string', 'max:500'],
            'other_nutrients'=> ['nullable', 'string'],
        ], [
            'product_name.required' => 'Nama produk wajib diisi.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $program = ScfProgram::getActive();

        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        // Cari kelompok user ini
        $member = ScfGroupMember::where('student_user_id', $user->id)
            ->whereHas('group', fn($q) => $q->where('program_id', $program->id))
            ->first();

        if (!$member) {
            return back()->with('error', 'Anda tidak terdaftar di kelompok manapun.');
        }

        $group = ScfGroup::find($member->group_id);

        // Hanya ketua yang boleh mengisi metadata
        if (!$group->isLeader($user->id)) {
            abort(403, 'Hanya ketua kelompok yang dapat mengisi metadata produk.');
        }

        // Cek apakah kelompok sudah difinalisasi
        if ($group->isFinalized()) {
            return back()->with('error', 'Nilai kelompok sudah difinalisasi. Metadata tidak dapat diubah.');
        }

        try {
            DB::connection('scf')->transaction(function () use ($request, $program, $group, $user) {
                $metadata = ScfProductMetadata::updateOrCreate(
                    ['program_id' => $program->id, 'group_id' => $group->id],
                    [
                        'product_name'    => $request->product_name,
                        'description'     => $request->description,
                        'ingredients'     => $request->ingredients,
                        'carbohydrate'    => $request->carbohydrate,
                        'protein'         => $request->protein,
                        'fat'             => $request->fat,
                        'other_nutrients' => $request->other_nutrients,
                        'created_by'      => $user->id,
                    ]
                );

                ScfActivityLog::log(
                    $user->id,
                    'product_metadata_saved',
                    "Ketua kelompok '{$group->name}' menyimpan metadata produk '{$request->product_name}'.",
                    $program->id
                );
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan metadata produk.');
        }

        return back()->with('success', 'Metadata produk berhasil disimpan.');
    }
}
