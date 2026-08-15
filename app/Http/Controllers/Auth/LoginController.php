<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ScfProgram;
use App\Models\ScfAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login SINFO.
     * Redirect jika sudah login.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectToRoleDashboard();
        }

        return view('auth.login');
    }

    /**
     * Proses login SINFO.
     *
     * Flow:
     * 1. Validasi input
     * 2. Cari user di auth_gara berdasarkan username
     * 3. Verifikasi password terhadap password_hash
     * 4. Cek status_aktif
     * 5. Buat Laravel session
     * 6. Load SCF assignment
     * 7. Redirect ke dashboard sesuai effective role
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari user di auth_gara
        $user = User::where('username', $credentials['username'])->first();

        // Validasi: user harus ada, password harus cocok, dan harus aktif
        if (!$user || !Hash::check($credentials['password'], $user->password_hash)) {
            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        if (!$user->isActive()) {
            throw ValidationException::withMessages([
                'username' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ]);
        }

        // Login user via Laravel Auth
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return $this->redirectToRoleDashboard();
    }

    /**
     * Tentukan dashboard berdasarkan effective SCF role user.
     */
    private function redirectToRoleDashboard(): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $effectiveRole = $user->getScfRole();

        return match ($effectiveRole) {
            'super_admin'     => redirect()->route('operator.dashboard'),
            'operator'        => redirect()->route('operator.dashboard'),
            'kepsek'          => redirect()->route('kepsek.dashboard'),
            'guru'            => redirect()->route('guru.dashboard'),
            'juri'            => redirect()->route('juri.dashboard'),
            'siswa'           => redirect()->route('siswa.dashboard'),
            'guru_unassigned' => redirect()->route('guru.dashboard'),
            default           => redirect()->route('login')->withErrors([
                'username' => 'Role tidak dikenal. Hubungi administrator.',
            ]),
        };
    }
}
