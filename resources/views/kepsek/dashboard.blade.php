@extends('layouts.app')

@section('title', 'Dashboard Kepala Sekolah')
@section('page-title', 'Monitoring SCF')

@section('nav-menu')
    <a href="{{ route('kepsek.dashboard') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('panduan.show') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2">
        <i class="ph ph-book-bookmark text-xl"></i> Panduan Projek IPA
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    <div class="bg-gradient-to-r from-slate-600 to-slate-800 rounded-2xl p-6 text-white shadow-soft">
        <p class="text-slate-300 text-sm">Monitoring</p>
        <h3 class="text-xl font-bold mt-1">Science Food Festival</h3>
        @if($program)
            <p class="text-slate-300 text-sm mt-1">{{ $program->name }} — {{ $program->academic_year }}</p>
        @endif
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card text-center">
            <h3 class="text-2xl font-bold text-gray-800">{{ $stats['group_count'] }}</h3>
            <p class="text-gray-500 text-xs mt-1">Total Kelompok</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card text-center">
            <h3 class="text-2xl font-bold text-blue-600">{{ $stats['poster_submitted'] }}</h3>
            <p class="text-gray-500 text-xs mt-1">Poster Masuk</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card text-center">
            <h3 class="text-2xl font-bold text-green-600">{{ $stats['assessed'] }}</h3>
            <p class="text-gray-500 text-xs mt-1">Penilaian Selesai</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Status Kelompok</h3>
        </div>
        @forelse($groups as $group)
        <div class="flex items-center justify-between p-4 border-b border-gray-50 hover:bg-gray-50">
            <p class="font-medium text-gray-800 text-sm">{{ $group->name }}</p>
            <div class="flex gap-2">
                @if($group->poster)
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full">Ada Poster</span>
                @endif
                @if($group->assessments->where('status','submitted')->count() > 0)
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Dinilai</span>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">Belum ada data kelompok.</div>
        @endforelse
    </div>

</div>
@endsection
