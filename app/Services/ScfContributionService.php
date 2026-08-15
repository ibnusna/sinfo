<?php

namespace App\Services;

use App\Models\ScfGroup;
use App\Models\ScfGroupMember;
use App\Models\ScfMemberContribution;
use App\Models\ScfActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * ScfContributionService
 *
 * Mengelola submission data kontribusi anggota kelompok.
 *
 * Mode A: all_contributed = true → semua anggota is_contributed = true
 * Mode B: ada anggota tidak aktif → ketua memilih siapa, dengan alasan wajib
 *
 * PRIVASI: Data kontribusi hanya boleh dilihat oleh Guru.
 * Juri, siswa anggota, dan kelompok lain TIDAK boleh melihatnya.
 */
class ScfContributionService
{
    /**
     * Submit data kontribusi anggota.
     *
     * @param ScfGroup $group
     * @param array    $data  Format:
     *   [
     *     'all_contributed' => bool,
     *     'members' => [
     *       ['student_user_id' => int, 'is_contributed' => bool, 'reason' => string|null],
     *       ...
     *     ]
     *   ]
     * @param User     $submitter  Harus ketua kelompok
     *
     * @return array ['ok' => bool, 'message' => string]
     */
    public function submitContributions(ScfGroup $group, array $data, User $submitter): array
    {
        // Validasi: submitter harus ketua kelompok
        if (!$group->isLeader($submitter->id)) {
            return ['ok' => false, 'message' => 'Hanya ketua kelompok yang dapat mengisi data kontribusi.'];
        }

        // Cek apakah sudah ada contribution yang sudah disubmit (terkunci)
        $existingSubmitted = ScfMemberContribution::where('group_id', $group->id)
            ->whereNotNull('submitted_at')
            ->exists();

        if ($existingSubmitted) {
            return ['ok' => false, 'message' => 'Data kontribusi sudah pernah disubmit dan terkunci. Hubungi guru jika perlu perubahan.'];
        }

        $allContributed = (bool) ($data['all_contributed'] ?? true);
        $memberInputs   = $data['members'] ?? [];

        // Ambil semua anggota kelompok
        $members = ScfGroupMember::where('group_id', $group->id)->get();
        $memberUserIds = $members->pluck('student_user_id')->toArray();

        // Validasi: Mode B — cek alasan untuk yang tidak berkontribusi
        if (!$allContributed) {
            foreach ($memberInputs as $input) {
                if (isset($input['is_contributed']) && !$input['is_contributed']) {
                    if (empty(trim($input['reason'] ?? ''))) {
                        $student = User::find($input['student_user_id']);
                        $name = $student?->getDisplayName() ?? "ID:{$input['student_user_id']}";
                        return [
                            'ok'      => false,
                            'message' => "Alasan wajib diisi untuk anggota yang tidak berkontribusi: {$name}.",
                        ];
                    }
                }
            }
        }

        try {
            DB::connection('scf')->transaction(function () use ($group, $submitter, $allContributed, $memberInputs, $memberUserIds) {
                $now = now();

                // Hapus data lama jika ada (draft yang belum submitted)
                ScfMemberContribution::where('group_id', $group->id)->delete();

                if ($allContributed) {
                    // Mode A: semua anggota berkontribusi penuh
                    foreach ($memberUserIds as $userId) {
                        ScfMemberContribution::create([
                            'program_id'       => $group->program_id,
                            'group_id'         => $group->id,
                            'student_user_id'  => $userId,
                            'is_contributed'   => true,
                            'reason'           => null,
                            'submitted_by'     => $submitter->id,
                            'submitted_at'     => $now,
                        ]);
                    }
                } else {
                    // Mode B: per anggota berdasarkan input ketua
                    $inputMap = collect($memberInputs)->keyBy('student_user_id');

                    foreach ($memberUserIds as $userId) {
                        $input = $inputMap->get($userId);
                        $isContributed = $input ? (bool) ($input['is_contributed'] ?? true) : true;
                        $reason        = ($isContributed ? null : (trim($input['reason'] ?? '') ?: null));

                        ScfMemberContribution::create([
                            'program_id'       => $group->program_id,
                            'group_id'         => $group->id,
                            'student_user_id'  => $userId,
                            'is_contributed'   => $isContributed,
                            'reason'           => $reason,
                            'submitted_by'     => $submitter->id,
                            'submitted_at'     => $now,
                        ]);
                    }
                }

                $modeLabel = $allContributed ? 'semua berkontribusi' : 'ada anggota tidak aktif';

                ScfActivityLog::log(
                    $submitter->id,
                    'contribution_submitted',
                    "Ketua kelompok '{$group->name}' mengisi kontribusi anggota ({$modeLabel}).",
                    $group->program_id
                );
            });

            return ['ok' => true, 'message' => 'Data kontribusi berhasil disimpan.'];

        } catch (\Exception $e) {
            return ['ok' => false, 'message' => 'Terjadi kesalahan saat menyimpan kontribusi: ' . $e->getMessage()];
        }
    }

    /**
     * Ambil data kontribusi untuk tampilan Guru.
     * HANYA guru yang boleh memanggil ini.
     *
     * Return collection of contributions dengan data siswa dari auth_gara.
     */
    public function getContributionsForGuru(ScfGroup $group): \Illuminate\Support\Collection
    {
        $contributions = ScfMemberContribution::forGroup($group->id);

        return $contributions->map(function ($contribution) {
            $student = User::find($contribution->student_user_id);
            $contribution->student_name = $student?->getDisplayName() ?? "ID:{$contribution->student_user_id}";
            return $contribution;
        });
    }
}
