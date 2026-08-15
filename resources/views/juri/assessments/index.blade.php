@extends('layouts.app')

@section('title', 'Penilaian Juri — Semua Kelompok')
@section('page-title', 'Penilaian Juri')

@section('nav-menu')
    <a href="{{ route('juri.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('juri.assessments.index') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-star text-xl"></i> Penilaian Juri
    </a>
@endsection

@section('content')
<div class="space-y-4 slide-up">
    @forelse($groups as $group)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-5 flex items-center justify-between hover:shadow-soft transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center">
                <i class="ph ph-users text-2xl text-yellow-500"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800">{{ $group->name }}</h4>
                <p class="text-sm text-gray-500">Ketua: {{ $group->leader_data?->getDisplayName() ?? '-' }}</p>
                @if($group->poster)
                    <span class="text-xs text-purple-600 font-medium"><i class="ph ph-image mr-1"></i>Poster tersedia</span>
                @else
                    <span class="text-xs text-gray-400"><i class="ph ph-image mr-1"></i>Belum ada poster</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($group->my_assessment?->isSubmitted())
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Submitted ✓</span>
            @elseif($group->my_assessment)
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Draft</span>
            @else
                <span class="px-3 py-1 bg-gray-100 text-gray-500 text-xs font-bold rounded-full">Belum Dinilai</span>
            @endif
            <a href="{{ route('juri.assessments.show', $group->id) }}"
                class="inline-flex items-center gap-1 px-4 py-2 bg-brand-teal text-white rounded-xl text-sm font-medium hover:bg-brand-teal-dark transition-colors">
                @if($group->my_assessment?->isSubmitted())
                    <i class="ph ph-eye"></i> Lihat
                @else
                    <i class="ph ph-pencil-simple"></i> Nilai
                @endif
            </a>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl p-12 border border-gray-100 text-center text-gray-400 shadow-card">
        <i class="ph ph-users text-4xl mb-3 block opacity-40"></i>
        <p>Belum ada kelompok dalam program ini.</p>
    </div>
    @endforelse
</div>
@endsection
