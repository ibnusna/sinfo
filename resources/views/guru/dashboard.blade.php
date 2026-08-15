@extends('layouts.app')

@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard Guru')

@section('nav-menu')
    <a href="{{ route('guru.dashboard') }}" class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('guru.groups.index') }}" class="nav-link {{ request()->routeIs('guru.groups.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-users text-xl"></i> Manajemen Kelompok
    </a>
    <a href="{{ route('guru.assessments.index') }}" class="nav-link {{ request()->routeIs('guru.assessments.*') || request()->routeIs('guru.scores.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-clipboard-text text-xl"></i> Penilaian IPA
    </a>
    <a href="{{ route('panduan.show') }}" target="_blank" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2 border-t border-gray-100">
        <i class="ph ph-book-bookmark text-xl text-brand-teal"></i> Panduan Projek IPA
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Greeting --}}
    <div class="bg-gradient-to-r from-brand-teal to-teal-600 rounded-2xl p-6 text-white shadow-soft">
        <p class="text-teal-100 text-sm">Selamat datang,</p>
        <h3 class="text-xl font-bold mt-1">{{ $user->getDisplayName() }}</h3>
        @if($program)
            <p class="text-teal-100 text-sm mt-1">{{ $program->name }} — {{ $program->academic_year }}</p>
        @endif
    </div>

    @if(!$program)
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-yellow-700">
            <p class="font-semibold">Belum ada program SCF aktif.</p>
        </div>
    @else

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card text-center">
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['group_count'] }}</h3>
            <p class="text-gray-500 text-xs mt-1">Kelompok Saya</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card text-center">
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['poster_count'] }}</h3>
            <p class="text-gray-500 text-xs mt-1">Poster Masuk</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card text-center">
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['assessed'] }}</h3>
            <p class="text-gray-500 text-xs mt-1">Sudah Dinilai</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <h3 class="font-bold text-gray-800 mb-4">Aksi Cepat</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('guru.groups.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-teal text-white rounded-xl text-sm font-semibold hover:bg-brand-teal-dark transition-colors">
                <i class="ph ph-plus"></i> Buat Kelompok
            </a>
            <a href="{{ route('guru.assessments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors">
                <i class="ph ph-clipboard-text"></i> Beri Penilaian
            </a>
        </div>
    </div>

    {{-- Recent Posters --}}
    @if($recentPosters->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Poster Terbaru</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($recentPosters as $poster)
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-purple-50 rounded-xl flex items-center justify-center">
                        <i class="ph ph-image text-purple-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $poster->group?->name }}</p>
                        <p class="text-xs text-gray-400">{{ $poster->original_name }}</p>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-full font-medium
                    {{ $poster->status === 'approved' ? 'bg-green-100 text-green-700' :
                       ($poster->status === 'submitted' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                    {{ ucfirst($poster->status) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Juklak & Juknis --}}
    @if($documents->count() > 0)
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <h3 class="font-bold text-gray-800 mb-3">Dokumen Program</h3>
        <div class="space-y-2">
            @foreach($documents as $doc)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <i class="ph ph-book-open-text text-brand-teal"></i>
                <div>
                    <span class="text-xs font-bold uppercase {{ $doc->type === 'juklak' ? 'text-blue-600' : 'text-purple-600' }}">{{ $doc->type }}</span>
                    <p class="text-sm font-medium text-gray-700">{{ $doc->title }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</div>
@endsection
