<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Cek apakah user yang login memiliki effective SCF role yang diizinkan.
     *
     * Cara penggunaan di route:
     *   ->middleware('role:operator')
     *   ->middleware('role:guru,operator')
     *   ->middleware('role:juri')
     *
     * Effective SCF role ditentukan oleh:
     * - Role GARA (operator, kepsek, super_admin) → akses langsung
     * - Guru dengan SCF assignment 'juri' → effective role = 'juri'
     * - Guru dengan SCF assignment 'guru' → effective role = 'guru'
     * - Siswa → effective role = 'siswa'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isActive()) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'username' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ]);
        }

        $effectiveRole = $user->getScfRole();

        // super_admin bisa akses semua
        if ($effectiveRole === 'super_admin') {
            return $next($request);
        }

        if (in_array($effectiveRole, $roles)) {
            return $next($request);
        }

        // Jika role tidak sesuai, redirect ke dashboard yang benar
        return $this->redirectToDashboard($effectiveRole);
    }

    private function redirectToDashboard(string $role): Response
    {
        $route = match ($role) {
            'operator'         => 'operator.dashboard',
            'guru'             => 'guru.dashboard',
            'juri'             => 'juri.dashboard',
            'siswa'            => 'siswa.dashboard',
            'kepsek'           => 'kepsek.dashboard',
            'guru_unassigned'  => 'guru.dashboard',
            default            => 'login',
        };

        try {
            return redirect()->route($route)->with('warning', 'Anda tidak memiliki akses ke halaman tersebut.');
        } catch (\Exception $e) {
            return redirect()->route('login');
        }
    }
}
