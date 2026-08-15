@extends('layouts.app')

@section('title', 'Manajemen Assignment')
@section('page-title', 'Manajemen Guru & Juri')

@section('nav-menu')
    <a href="{{ route('operator.dashboard') }}" class="nav-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('operator.assignments.index') }}" class="nav-link {{ request()->routeIs('operator.assignments.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-users-three text-xl"></i> Manajemen Guru & Juri
    </a>
    <a href="{{ route('operator.markdown.index') }}" class="nav-link {{ request()->routeIs('operator.markdown.*') || request()->routeIs('operator.documents.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-book-open-text text-xl"></i> Panduan Projek (Markdown)
    </a>
    <a href="{{ route('operator.landing_photos.index') }}" class="nav-link {{ request()->routeIs('operator.landing_photos.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-image text-xl"></i> Foto Landing Page
    </a>
    <a href="{{ route('operator.score_settings.index') }}" class="nav-link {{ request()->routeIs('operator.score_settings.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-sliders text-xl"></i> Konfigurasi Penilaian
    </a>
    <a href="{{ route('panduan.show') }}" target="_blank" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2 border-t border-gray-100">
        <i class="ph ph-eye text-xl text-brand-teal"></i> Lihat Panduan Publik
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Add Assignment Form --}}
    @if($program)
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <h3 class="font-bold text-gray-800 mb-4">Tambah Assignment</h3>
        <form method="POST" action="{{ route('operator.assignments.store') }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <select name="user_id" required class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none">
                <option value="">-- Pilih Guru --</option>
                @foreach($availableGurus as $guru)
                    <option value="{{ $guru->id }}">{{ $guru->getDisplayName() }} ({{ $guru->username }})</option>
                @endforeach
            </select>
            <select name="assignment_type" required class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal outline-none">
                <option value="guru">Guru SCF</option>
                <option value="juri">Juri Festival</option>
            </select>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl font-semibold text-sm transition-colors whitespace-nowrap">
                <i class="ph ph-plus"></i> Tambahkan
            </button>
        </form>
        @if($availableGurus->isEmpty())
            <p class="text-sm text-gray-400 mt-2">Semua guru sudah memiliki assignment di program ini.</p>
        @endif
    </div>
    @endif

    {{-- Assignment List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Daftar Assignment Aktif</h3>
        </div>

        @if($assignments->isEmpty())
            <div class="p-12 text-center text-gray-400">
                <i class="ph ph-users-three text-4xl mb-3 block opacity-40"></i>
                <p>Belum ada assignment untuk program ini.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($assignments as $assignment)
                <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-teal-light text-brand-teal flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($assignment->user?->getDisplayName() ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-sm">{{ $assignment->user?->getDisplayName() ?? 'User tidak ditemukan' }}</p>
                            <p class="text-xs text-gray-400">{{ $assignment->user?->username }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($assignment->assignment_type === 'juri')
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">⭐ Juri</span>
                        @else
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">Guru SCF</span>
                        @endif
                        <form method="POST" action="{{ route('operator.assignments.destroy', $assignment->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete(this.form)"
                                class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="ph ph-trash text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function confirmDelete(form) {
    Swal.fire({
        title: 'Hapus Assignment?',
        text: 'Assignment ini akan dihapus dari program.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush
