@extends('layouts.app')

@section('title', 'Dashboard Juri')
@section('page-title', 'Dashboard Juri')

@section('nav-menu')
    <a href="{{ route('juri.dashboard') }}" class="nav-link {{ request()->routeIs('juri.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('juri.assessments.index') }}" class="nav-link {{ request()->routeIs('juri.assessments.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-star text-xl"></i> Penilaian Juri
    </a>
    <a href="{{ route('panduan.show') }}" target="_blank" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2 border-t border-gray-100">
        <i class="ph ph-book-bookmark text-xl text-brand-teal"></i> Panduan Projek IPA
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    <div class="bg-gradient-to-r from-yellow-400 to-orange-400 rounded-2xl p-6 text-white shadow-soft">
        <p class="text-yellow-100 text-sm">Selamat datang, Juri</p>
        <h3 class="text-xl font-bold mt-1">{{ $user->getDisplayName() }}</h3>
        @if($program)
            <p class="text-yellow-100 text-sm mt-1">{{ $program->name }}</p>
        @endif
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card text-center">
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_groups'] }}</h3>
            <p class="text-gray-500 text-xs mt-1">Total Kelompok</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card text-center">
            <h3 class="text-2xl font-bold text-green-600">{{ $stats['assessed'] }}</h3>
            <p class="text-gray-500 text-xs mt-1">Sudah Dinilai</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card text-center">
            <h3 class="text-2xl font-bold text-orange-500">{{ $stats['pending'] }}</h3>
            <p class="text-gray-500 text-xs mt-1">Belum Dinilai</p>
        </div>
    </div>

    {{-- Groups List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Daftar Kelompok</h3>
        </div>
        @forelse($groups as $group)
        <div class="flex items-center justify-between p-4 hover:bg-gray-50 border-b border-gray-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-yellow-50 rounded-xl flex items-center justify-center">
                    <i class="ph ph-users text-yellow-500"></i>
                </div>
                <div>
                    <p class="font-semibold text-sm text-gray-800">{{ $group->name }}</p>
                    <p class="text-xs text-gray-400">Ketua: {{ $group->leader_data?->getDisplayName() ?? '-' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($group->my_assessment?->status === 'submitted')
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Submitted ✓</span>
                @elseif($group->my_assessment)
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Draft</span>
                @endif
                <a href="{{ route('juri.assessments.show', $group->id) }}"
                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-brand-teal text-white rounded-lg text-xs font-medium hover:bg-brand-teal-dark transition-colors">
                    <i class="ph ph-arrow-right"></i>
                    {{ $group->my_assessment?->isSubmitted() ? 'Lihat' : 'Nilai' }}
                </a>
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-gray-400">
            <p>Belum ada kelompok dalam program ini.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection
