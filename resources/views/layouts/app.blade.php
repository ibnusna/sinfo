<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SINFO</title>

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
                            teal: '#0d9488', 'teal-dark': '#0f766e',
                            'teal-light': '#ccfbf1', yellow: '#facc15',
                            dark: '#1f2937', light: '#f9fafb',
                        }
                    },
                    boxShadow: {
                        'soft': '0 10px 40px -10px rgba(13,148,136,0.1)',
                        'card': '0 4px 20px -2px rgba(0,0,0,0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f3f4f6; overflow-x: hidden; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        .slide-up { animation: slideUp 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .glass { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); }
        .nav-link.active { background-color: #ccfbf1; color: #0d9488; font-weight: 600; }
    </style>
    @stack('styles')
</head>
<body class="text-gray-800 antialiased h-screen flex overflow-hidden selection:bg-brand-teal selection:text-white">

    <div class="w-full h-full flex bg-gray-50 relative">

        {{-- ── SIDEBAR ── --}}
        <aside id="sidebar" class="w-72 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 absolute md:relative z-40 h-full -translate-x-full md:translate-x-0">

            {{-- Logo --}}
            <div class="h-20 flex items-center px-6 border-b border-gray-100 justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-brand-teal rounded-lg flex items-center justify-center text-white">
                        <i class="ph-bold ph-flask text-lg"></i>
                    </div>
                    <span class="font-bold text-lg text-brand-dark tracking-tight">SINFO.</span>
                </div>
                <button id="close-sidebar" class="md:hidden text-gray-500 hover:text-red-500">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            {{-- Navigation --}}
            <div class="flex-1 overflow-y-auto py-6 px-4 no-scrollbar space-y-1">
                @yield('nav-menu')
            </div>

            {{-- User Info + Logout --}}
            <div class="p-4 border-t border-gray-100">
                <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-gray-50 border border-gray-100">
                    <div class="w-10 h-10 rounded-full bg-brand-teal-light text-brand-teal flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->getDisplayName(), 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->getDisplayName() }}</p>
                        <p class="text-xs text-gray-500 truncate capitalize">{{ auth()->user()->getScfRole() }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors font-medium">
                        <i class="ph ph-sign-out"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- ── MAIN CONTENT ── --}}
        <main class="flex-1 flex flex-col h-full overflow-hidden relative w-full">

            {{-- Mobile Overlay --}}
            <div id="sidebar-overlay" class="fixed inset-0 bg-gray-900/50 z-30 hidden md:hidden transition-opacity opacity-0"></div>

            {{-- TOPBAR --}}
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 z-20 shrink-0">
                <div class="flex items-center gap-4">
                    <button id="open-sidebar" class="md:hidden text-gray-600 hover:text-brand-teal p-2 rounded-lg bg-gray-50">
                        <i class="ph ph-list text-2xl"></i>
                    </button>
                    <h2 class="text-xl font-bold text-brand-dark hidden sm:block">@yield('page-title', 'Dashboard')</h2>
                </div>

                <div class="flex items-center gap-4">
                    {{-- System Status Badge --}}
                    @if(isset($systemStatus))
                        @if($systemStatus === 'open')
                            <div class="px-3 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-sm bg-green-100 text-green-700">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse inline-block"></span>
                                Sistem Terbuka
                            </div>
                        @else
                            <div class="px-3 py-1.5 rounded-full text-xs font-bold flex items-center gap-1.5 shadow-sm bg-red-100 text-red-700">
                                <span class="w-2 h-2 bg-red-500 rounded-full inline-block"></span>
                                Sistem Ditutup
                            </div>
                        @endif
                    @endif
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div id="flash-success" class="mx-4 mt-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2 fade-in">
                    <i class="ph ph-check-circle text-lg"></i>
                    {{ session('success') }}
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700"><i class="ph ph-x"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div id="flash-error" class="mx-4 mt-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center gap-2 fade-in">
                    <i class="ph ph-warning-circle text-lg"></i>
                    {{ session('error') }}
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-500 hover:text-red-700"><i class="ph ph-x"></i></button>
                </div>
            @endif
            @if(session('warning'))
                <div class="mx-4 mt-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl text-sm flex items-center gap-2 fade-in">
                    <i class="ph ph-warning text-lg"></i>
                    {{ session('warning') }}
                </div>
            @endif

            {{-- System Closed Notice (non-operator) --}}
            @if(isset($systemStatus) && $systemStatus === 'closed' && !in_array(auth()->user()->getScfRole(), ['operator','super_admin']))
                <div class="mx-4 mt-4 p-4 bg-orange-50 border border-orange-200 text-orange-700 rounded-xl text-sm flex items-center gap-2">
                    <i class="ph ph-lock-key text-lg"></i>
                    <span>Sistem SCF saat ini <strong>ditutup</strong>. Beberapa fitur tidak tersedia. Hubungi operator untuk informasi lebih lanjut.</span>
                </div>
            @endif

            {{-- PAGE CONTENT --}}
            <div class="flex-1 overflow-y-auto p-4 lg:p-8 fade-in">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Mobile sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('open-sidebar');
        const closeBtn = document.getElementById('close-sidebar');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            setTimeout(() => overlay.classList.remove('opacity-0'), 10);
        }
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 200);
        }

        openBtn?.addEventListener('click', openSidebar);
        closeBtn?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        // Auto-dismiss flash messages after 5s
        setTimeout(() => {
            document.getElementById('flash-success')?.remove();
            document.getElementById('flash-error')?.remove();
        }, 5000);

        // CSRF setup for AJAX
        window.CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;
    </script>
    @stack('scripts')
</body>
</html>
