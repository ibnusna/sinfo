<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ScfProgram;
use App\Models\ScfSetting;
use Symfony\Component\HttpFoundation\Response;

class SystemOpenMiddleware
{
    /**
     * Cek apakah sistem SCF dalam status OPEN.
     * Jika CLOSED, hanya operator yang bisa melakukan operasi write.
     *
     * Middleware ini digunakan pada route yang memerlukan sistem terbuka
     * (upload poster, submit penilaian, dll).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $effectiveRole = $user->getScfRole();

        // Operator selalu bisa akses
        if (in_array($effectiveRole, ['operator', 'super_admin'])) {
            return $next($request);
        }

        // Cek status sistem dari program aktif
        $program = ScfProgram::getActive();
        if (!$program) {
            return redirect()->back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        $status = ScfSetting::getSystemStatus($program->id);

        if ($status !== 'open') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sistem Science Food Festival sedang ditutup. Hubungi operator.',
                ], 403);
            }

            return redirect()->back()->with('error', 'Sistem Science Food Festival sedang ditutup. Hubungi operator untuk informasi lebih lanjut.');
        }

        return $next($request);
    }
}
