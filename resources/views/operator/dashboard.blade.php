@extends('layouts.app')

@section('title', 'Dashboard Operator')
@section('page-title', 'Dashboard Operator')

@section('nav-menu')
    <a href="{{ route('operator.dashboard') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('operator.assignments.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-users-three text-xl"></i> Manajemen Guru & Juri
    </a>
    <a href="{{ route('operator.documents.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-book-open-text text-xl"></i> Juklak & Juknis
    </a>
    <a href="{{ route('operator.landing_photos.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-image text-xl"></i> Foto Landing Page
    </a>
    <a href="{{ route('operator.markdown.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-file-md text-xl"></i> Markdown Panduan
    </a>
    <a href="{{ route('operator.score_settings.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-sliders text-xl"></i> Konfigurasi Penilaian
    </a>
    <a href="{{ route('panduan.show') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2">
        <i class="ph ph-book-bookmark text-xl"></i> Panduan Projek IPA
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Program Header --}}
    @if($program)
        <div class="bg-gradient-to-r from-brand-teal to-teal-600 rounded-2xl p-6 text-white shadow-soft">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-teal-100 text-sm font-medium">Program Aktif</p>
                    <h3 class="text-2xl font-bold mt-1">{{ $program->name }}</h3>
                    <p class="text-teal-100 text-sm mt-1">Tahun Ajaran {{ $program->academic_year }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($systemStatus === 'open')
                        <span class="inline-flex items-center gap-2 bg-white/20 rounded-full px-4 py-2 text-sm font-bold">
                            <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span> Sistem Terbuka
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 bg-white/20 rounded-full px-4 py-2 text-sm font-bold">
                            <span class="w-2 h-2 bg-red-300 rounded-full"></span> Sistem Ditutup
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-yellow-700">
            <i class="ph ph-warning text-2xl mb-2"></i>
            <p class="font-semibold">Belum ada program SCF aktif.</p>
            <p class="text-sm mt-1">Hubungi Superadmin untuk mengaktifkan program.</p>
        </div>
    @endif

    {{-- Kontrol Sistem --}}
    @if($program)
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <h3 class="font-bold text-gray-800 mb-1">Kontrol Sistem</h3>
        <p class="text-sm text-gray-500 mb-4">Mengatur apakah guru, juri, dan siswa dapat melakukan aktivitas SCF.</p>
        <form method="POST" action="{{ route('operator.system.toggle') }}" id="form-system-toggle">
            @csrf
            <input type="hidden" name="status" id="toggle-status" value="{{ $systemStatus === 'open' ? 'closed' : 'open' }}">
            @if($systemStatus === 'open')
                <button type="button" onclick="confirmToggle('closed')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                    <i class="ph ph-lock-key"></i> Tutup Sistem
                </button>
            @else
                <button type="button" onclick="confirmToggle('open')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                    <i class="ph ph-lock-key-open"></i> Buka Sistem
                </button>
            @endif
        </form>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mb-3">
                <i class="ph ph-chalkboard-teacher text-xl text-blue-500"></i>
            </div>
            <p class="text-gray-500 text-sm">Guru SCF</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['guru_count'] }}</h3>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card">
            <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center mb-3">
                <i class="ph ph-star text-xl text-yellow-500"></i>
            </div>
            <p class="text-gray-500 text-sm">Juri Festival</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['juri_count'] }}</h3>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card">
            <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center mb-3">
                <i class="ph ph-users text-xl text-teal-500"></i>
            </div>
            <p class="text-gray-500 text-sm">Kelompok</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['group_count'] }}</h3>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center mb-3">
                <i class="ph ph-image text-xl text-purple-500"></i>
            </div>
            <p class="text-gray-500 text-sm">Poster Masuk</p>
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['poster_submitted'] }}<span class="text-gray-400 text-base font-normal">/{{ $stats['poster_count'] }}</span></h3>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <h3 class="font-bold text-gray-800 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <a href="{{ route('operator.assignments.index') }}" class="p-4 border border-gray-200 rounded-xl flex flex-col items-center justify-center gap-2 hover:bg-brand-teal-light hover:border-brand-teal transition-colors text-gray-700">
                <i class="ph ph-users-three text-2xl text-brand-teal"></i>
                <span class="text-xs font-semibold text-center">Kelola Assignment</span>
            </a>
            <a href="{{ route('operator.documents.index') }}" class="p-4 border border-gray-200 rounded-xl flex flex-col items-center justify-center gap-2 hover:bg-brand-teal-light hover:border-brand-teal transition-colors text-gray-700">
                <i class="ph ph-book-open-text text-2xl text-brand-teal"></i>
                <span class="text-xs font-semibold text-center">Kelola Juklak/Juknis</span>
            </a>
            <a href="{{ route('operator.assignments.index') }}" class="p-4 border border-gray-200 rounded-xl flex flex-col items-center justify-center gap-2 hover:bg-brand-teal-light hover:border-brand-teal transition-colors text-gray-700">
                <i class="ph ph-star text-2xl text-brand-teal"></i>
                <span class="text-xs font-semibold text-center">Kelola Juri</span>
            </a>
        </div>
    </div>

    {{-- Activity Log --}}
    @if($recentLogs->count() > 0)
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <h3 class="font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>
        <div class="space-y-3">
            @foreach($recentLogs as $log)
            <div class="flex items-start gap-3 text-sm">
                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                    <i class="ph ph-activity text-gray-500 text-sm"></i>
                </div>
                <div>
                    <p class="text-gray-700">{{ $log->description }}</p>
                    <p class="text-gray-400 text-xs mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function confirmToggle(newStatus) {
    const label = newStatus === 'open' ? 'MEMBUKA' : 'MENUTUP';
    Swal.fire({
        title: `${label} Sistem?`,
        text: newStatus === 'closed'
            ? 'Guru, juri, dan siswa tidak dapat melakukan aktivitas SCF.'
            : 'Guru, juri, dan siswa dapat kembali melakukan aktivitas SCF.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: newStatus === 'open' ? '#22c55e' : '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Ya, ${label}`,
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('toggle-status').value = newStatus;
            document.getElementById('form-system-toggle').submit();
        }
    });
}
</script>
@endpush
