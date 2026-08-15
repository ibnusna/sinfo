<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — SINFO Science Food Festival</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        brand: {
                            teal: '#0d9488',
                            'teal-dark': '#0f766e',
                            'teal-light': '#ccfbf1',
                            yellow: '#facc15',
                            dark: '#1f2937',
                            light: '#f9fafb',
                        }
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(13, 148, 136, 0.1)',
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f3f4f6; font-family: 'Poppins', sans-serif; }
        .slide-up { animation: slideUp 0.4s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative overflow-hidden bg-brand-light">

    <!-- Background decorations -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-teal-light rounded-full blur-[100px] opacity-50 -z-10"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-yellow-100 rounded-full blur-[80px] opacity-50 -z-10"></div>

    <div class="bg-white p-10 rounded-3xl shadow-soft w-full max-w-md mx-4 slide-up border border-gray-100 relative overflow-hidden">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-teal rounded-2xl flex items-center justify-center text-white shadow-soft mx-auto mb-4">
                <i class="ph-bold ph-flask text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-brand-dark">SINFO</h1>
            <p class="text-sm text-gray-500">Science Food Festival Info System</p>
        </div>

        {{-- Session Success --}}
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <div class="relative">
                    <i class="ph ph-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        value="{{ old('username') }}"
                        autocomplete="username"
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none transition-all @error('username') border-red-400 @enderror"
                        placeholder="Masukkan username"
                        required
                    >
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <i class="ph ph-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        autocomplete="current-password"
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none transition-all @error('password') border-red-400 @enderror"
                        placeholder="••••••••"
                        required
                    >
                </div>
            </div>

            <button
                type="submit"
                id="btn-login"
                class="w-full bg-brand-teal hover:bg-brand-teal-dark text-white font-semibold py-3 rounded-xl shadow-md transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2"
            >
                <i class="ph ph-sign-in"></i>
                Login Sistem
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <p class="text-xs text-gray-400">
                Gunakan akun GARA Anda untuk masuk ke sistem SINFO.
            </p>
        </div>
    </div>

    <script>
        // Submit button loading state
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-login');
            btn.innerHTML = '<i class="ph ph-circle-notch animate-spin"></i> Memproses...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
