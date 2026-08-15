<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfGroup;
use App\Services\ScfScoreCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinalizeController extends Controller
{
    public function __construct(
        private ScfScoreCalculationService $scoreService
    ) {}

    /**
     * Finalisasi nilai kelompok.
     *
     * Prasyarat:
     * 1. Poster sudah ada.
     * 2. Metadata produk sudah ada.
     * 3. Penilaian poster sudah submitted.
     * 4. Penilaian IPA sudah submitted.
     * 5. Minimal satu penilaian juri sudah submitted.
     * 6. Kontribusi anggota sudah disubmit.
     *
     * Setelah finalisasi, nilai TERKUNCI dan tidak dapat diubah.
     */
    public function finalize(Request $request, int $groupId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $group = ScfGroup::with(['members', 'poster', 'productMetadata'])->findOrFail($groupId);

        // Hanya guru yang membuat kelompok yang bisa finalize
        if ($group->created_by !== $user->id) {
            abort(403, 'Anda tidak berhak memfinalisasi nilai kelompok ini.');
        }

        $result = $this->scoreService->finalizeScores($group, $user);

        if (!$result['ok']) {
            return back()->with('error', $result['message']);
        }

        $warningMsg = '';
        if (!$result['weights_configured']) {
            $warningMsg = ' Catatan: bobot komponen belum dikonfigurasi oleh operator — menggunakan equal weight (33.33%).';
        }

        return redirect()->route('guru.scores.show', $groupId)
            ->with('success', "Nilai kelompok '{$group->name}' berhasil difinalisasi. Group score: " . number_format($result['group_score'], 2) . '.' . $warningMsg);
    }
}
