@extends('layouts.app')

@section('title', 'Penilaian IPA & Poster')
@section('page-title', 'Penilaian Kelompok')

@section('nav-menu')
    <a href="{{ route('guru.dashboard') }}" class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('guru.groups.index') }}" class="nav-link {{ request()->routeIs('guru.groups.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-users text-xl"></i> Manajemen Kelompok
    </a>
    <a href="{{ route('guru.assessments.index') }}" class="nav-link {{ request()->routeIs('guru.assessments.*') || request()->routeIs('guru.scores.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-clipboard-text text-xl"></i> Penilaian IPA
    </a>
    <a href="{{ route('panduan.show') }}" target="_blank" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2 border-t border-gray-100">
        <i class="ph ph-book-bookmark text-xl text-brand-teal"></i> Panduan Projek IPA
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- ══════════════════════════════════════════
         DASHBOARD GRID KELOMPOK (CARD VIEW)
         ══════════════════════════════════════════ --}}
    <div id="guru-assessment-dashboard" class="space-y-6">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-gray-800">Daftar Kelompok Projek</h3>
                <p class="text-xs text-gray-500 mt-0.5">Pilih salah satu kelompok untuk mulai menilai IPA & Poster</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:flex-none md:w-64">
                    <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="group-search" onkeyup="filterGroups()"
                           placeholder="Cari kelompok / ketua..."
                           class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2 text-xs focus:border-brand-teal outline-none transition-all">
                </div>
                <div class="w-full md:w-48">
                    <select id="group-class-filter" onchange="filterGroups()"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs focus:border-brand-teal outline-none transition-all text-gray-600 font-medium bg-white">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="groups-grid">
            @forelse($groups as $group)
            <div class="group-card bg-white rounded-2xl border border-gray-100 shadow-card hover:shadow-soft transition-all duration-300 overflow-hidden flex flex-col justify-between"
                 data-name="{{ strtolower($group->name) }}"
                 data-leader="{{ strtolower($group->leader_data?->getDisplayName() ?? '') }}"
                 data-class="{{ $group->leader_data?->siswa?->kelas?->nama_kelas ?? 'Tanpa Kelas' }}">
                <div class="p-5 space-y-4">
                    {{-- Header --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h4 class="font-bold text-gray-800 text-sm leading-tight truncate">{{ $group->name }}</h4>
                            <p class="text-xs text-gray-400 mt-1">
                                Ketua: <span class="font-semibold text-gray-700">{{ $group->leader_data?->getDisplayName() ?? '-' }}</span>
                            </p>
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-brand-teal-light text-brand-teal shrink-0">
                            {{ $group->leader_data?->siswa?->kelas?->nama_kelas ?? '-' }}
                        </span>
                    </div>

                    {{-- Badges/Statuses --}}
                    <div class="space-y-2 pt-3 border-t border-gray-50">
                        {{-- Poster Status --}}
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-400 flex items-center gap-1.5"><i class="ph ph-image"></i> Poster:</span>
                            @if($group->poster)
                                @if($group->poster->status === 'approved')
                                    <span class="text-green-600 font-bold">Disetujui ✓</span>
                                @elseif($group->poster->status === 'revision')
                                    <span class="text-orange-600 font-bold">Revisi ⚠</span>
                                @else
                                    <span class="text-blue-600 font-bold">Menunggu</span>
                                @endif
                            @else
                                <span class="text-gray-400 font-medium">Belum Upload</span>
                            @endif
                        </div>

                        {{-- Penilaian Poster Status --}}
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-400 flex items-center gap-1.5"><i class="ph ph-palette"></i> Nilai Poster:</span>
                            @if($group->poster_assessment?->isSubmitted())
                                <span class="text-green-600 font-bold">Submitted ({{ number_format($group->poster_assessment->calculateWeightedScore(), 1) }})</span>
                            @elseif($group->poster_assessment)
                                <span class="text-yellow-600 font-bold">Draft</span>
                            @else
                                <span class="text-gray-400 font-medium">Belum Dinilai</span>
                            @endif
                        </div>

                        {{-- Penilaian IPA Status --}}
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-400 flex items-center gap-1.5"><i class="ph ph-atom"></i> Nilai IPA:</span>
                            @if($group->my_assessment?->isSubmitted())
                                <span class="text-green-600 font-bold">Submitted ({{ number_format($group->my_assessment->calculateWeightedScore(), 1) }})</span>
                            @elseif($group->my_assessment)
                                <span class="text-yellow-600 font-bold">Draft</span>
                            @else
                                <span class="text-gray-400 font-medium">Belum Dinilai</span>
                            @endif
                        </div>

                        {{-- Juri Assessments Status --}}
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-400 flex items-center gap-1.5"><i class="ph ph-star"></i> Nilai Juri (Makanan):</span>
                            @if($group->juri_assessments->count() > 0)
                                <span class="text-brand-teal font-bold">{{ $group->juri_assessments->count() }} Juri</span>
                            @else
                                <span class="text-gray-400 font-medium">Belum Dinilai</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="bg-gray-50 border-t border-gray-100 p-4 flex gap-2">
                    <button type="button" onclick="showGroupAssessment({{ $group->id }})"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl text-xs font-semibold transition-colors">
                        <i class="ph ph-clipboard-text text-sm"></i> Nilai Kelompok
                    </button>
                    <a href="{{ route('guru.scores.show', $group->id) }}"
                       class="inline-flex items-center justify-center p-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-semibold transition-colors"
                       title="Rekap Nilai">
                        <i class="ph ph-chart-bar text-base"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-2xl p-12 border border-gray-100 text-center text-gray-400 shadow-card col-span-full">
                <i class="ph ph-users text-4xl mb-3 block opacity-40"></i>
                <p class="font-medium">Belum ada kelompok untuk dinilai.</p>
                <a href="{{ route('guru.groups.index') }}" class="text-brand-teal text-xs mt-2 inline-block hover:underline">Buat kelompok baru →</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         DETAIL FORM PENILAIAN (DYNAMIC DETAIL VIEW)
         ══════════════════════════════════════════ --}}
    <div id="group-assessment-details-container" class="hidden space-y-6">
        @foreach($groups as $group)
        <div id="group-detail-{{ $group->id }}" class="group-detail-panel hidden space-y-6">
            
            {{-- Navigation Action Bar --}}
            <div class="flex items-center justify-between bg-white rounded-2xl p-4 border border-gray-100 shadow-card flex-wrap gap-2">
                <button type="button" onclick="hideGroupAssessment()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-bold transition-all">
                    <i class="ph ph-arrow-left"></i> Kembali ke Daftar Kelompok
                </button>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">Sedang menilai:</span>
                    <h4 class="font-bold text-gray-800 text-xs bg-brand-teal-light text-brand-teal px-3 py-1.5 rounded-xl">{{ $group->name }}</h4>
                </div>
            </div>

            {{-- Form Penilaian --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between gap-3">
                    <div>
                        <h4 class="font-bold text-gray-800 text-base">{{ $group->name }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $group->members->count() }} Anggota
                            @if($group->productMetadata)
                                · <span class="text-brand-teal font-medium">{{ $group->productMetadata->product_name }}</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('guru.scores.show', $group->id) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-semibold transition-colors">
                        <i class="ph ph-chart-bar"></i> Rekap Nilai
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">

                    {{-- PANEL KIRI: PENILAIAN POSTER --}}
                    <div>
                        <div class="px-5 py-3 bg-purple-50 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="ph ph-image text-purple-600"></i>
                                <span class="text-sm font-bold text-purple-700">Penilaian Poster</span>
                            </div>
                            @if($group->poster_assessment?->isSubmitted())
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                                    <i class="ph ph-check-circle"></i> Submitted
                                </span>
                            @elseif($group->poster_assessment)
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Draft</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-bold rounded-full">Belum</span>
                            @endif
                        </div>

                        @if($group->poster)
                        <div class="px-5 py-3 bg-purple-50/30 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <i class="ph ph-file-text text-purple-600 text-lg"></i>
                                <a href="{{ Storage::disk('public')->url($group->poster->file_path) }}" target="_blank"
                                   class="text-xs font-semibold text-purple-700 hover:underline truncate" title="Lihat Poster">
                                    {{ $group->poster->original_name }}
                                </a>
                                <span class="text-[11px] text-gray-400">({{ $group->poster->getFileSizeFormatted() }})</span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($group->poster->status === 'approved')
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                                        <i class="ph ph-check-circle"></i> Poster Disetujui
                                    </span>
                                @elseif($group->poster->status === 'revision')
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-bold rounded-full flex items-center gap-1">
                                        <i class="ph ph-warning-circle"></i> Perlu Revisi
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full flex items-center gap-1">
                                        <i class="ph ph-clock"></i> Menunggu Verifikasi
                                    </span>
                                @endif

                                {{-- Action buttons for poster verification --}}
                                <div class="flex items-center gap-1 ml-2">
                                    <form method="POST" action="{{ route('guru.posters.status', $group->poster->id) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" title="Setujui Poster"
                                            class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1">
                                            <i class="ph ph-check"></i> Setujui
                                        </button>
                                    </form>
                                    <button type="button" onclick="requestPosterRevision({{ $group->poster->id }}, '{{ addslashes($group->name) }}')"
                                        title="Minta Revisi Poster"
                                        class="px-2 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1">
                                        <i class="ph ph-arrow-counter-clockwise"></i> Revisi
                                    </button>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="px-5 py-2.5 bg-gray-50 border-b border-gray-100 text-xs text-gray-400 flex items-center gap-2">
                            <i class="ph ph-warning text-amber-400"></i> Poster belum diupload oleh siswa.
                        </div>
                        @endif

                        @if(!$group->poster_assessment?->isSubmitted())
                        <form method="POST" action="{{ route('guru.assessments.store') }}" class="p-5 assessment-form" id="poster-form-{{ $group->id }}" data-group-id="{{ $group->id }}" data-type="poster">
                            @csrf
                            <input type="hidden" name="group_id" value="{{ $group->id }}">
                            <input type="hidden" name="assessment_type" value="poster">

                            @if($posterCriteria->isEmpty())
                                <p class="text-sm text-gray-400 italic">Kriteria poster belum dikonfigurasi.</p>
                            @else
                            <div class="flex items-center justify-between mb-3 bg-purple-50/50 p-2.5 rounded-xl border border-purple-100">
                                <span class="text-xs font-bold text-purple-800">Isi Nilai Kriteria Poster</span>
                                <button type="button" onclick="presetAllScores('poster-form-{{ $group->id }}', 75)"
                                        class="px-2 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg text-[11px] font-semibold transition-colors">
                                    <i class="ph ph-magic-wand mr-0.5"></i> Set Semua Default (75)
                                </button>
                            </div>

                            <div class="space-y-3 mb-4">
                                @foreach($posterCriteria as $criterion)
                                @php
                                    $existingScore = $group->poster_assessment?->details?->firstWhere('criterion', $criterion->name)?->score;
                                    $defaultScore = $existingScore !== null ? $existingScore : 75;
                                @endphp
                                <div class="bg-gray-50/80 rounded-xl p-3 border border-gray-100 hover:border-purple-200 transition-colors criterion-card"
                                     data-weight="{{ $criterion->weight }}" data-max="{{ $criterion->max_score }}">
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-xs text-gray-800">{{ $criterion->name }}
                                                <span class="text-purple-600 font-bold ml-1">({{ $criterion->weight }}%)</span>
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <input type="number" name="scores[{{ $criterion->name }}]"
                                                value="{{ (int)$defaultScore }}"
                                                min="0" max="{{ (int)$criterion->max_score }}" required
                                                oninput="calculateLiveScore('poster-form-{{ $group->id }}')"
                                                class="score-input w-16 border border-gray-200 rounded-lg px-2 py-1 text-xs font-bold text-center text-purple-700 focus:border-purple-500 focus:ring-1 focus:ring-purple-200 outline-none">
                                            <span class="text-xs text-gray-400 font-medium">/{{ (int)$criterion->max_score }}</span>
                                        </div>
                                    </div>
                                    {{-- Quick Preset Buttons --}}
                                    <div class="flex items-center justify-between pt-1 border-t border-gray-100/60 mt-1.5">
                                        <span class="text-[10px] text-gray-400">Preset cepat:</span>
                                        <div class="flex items-center gap-1">
                                            @foreach([70, 75, 80, 85, 90, 100] as $preset)
                                            <button type="button" onclick="setScoreInput(this, {{ $preset }}, 'poster-form-{{ $group->id }}')"
                                                    class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white border border-gray-200 hover:bg-purple-600 hover:text-white hover:border-purple-600 transition-all text-gray-600">
                                                {{ $preset }}
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Live Score Preview Card --}}
                            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-3.5 text-white shadow-sm mb-4 flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] uppercase tracking-wider font-semibold opacity-90">Auto Grading (Prediksi Total)</p>
                                    <div class="flex items-baseline gap-2 mt-0.5">
                                        <span class="text-2xl font-extrabold live-total-score">75.00</span>
                                        <span class="text-xs opacity-80">/ 100</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm live-grade-badge">
                                        Baik (B)
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" name="action" value="draft"
                                    class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition-colors flex items-center gap-1">
                                    <i class="ph ph-floppy-disk text-sm"></i> Simpan Draft
                                </button>
                                <button type="button" onclick="confirmSubmit(this.closest('form'), 'Poster')"
                                    class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold transition-all shadow-sm flex items-center gap-1">
                                    <i class="ph ph-paper-plane-tilt text-sm"></i> Submit Penilaian
                                </button>
                            </div>
                            @endif
                        </form>
                        @else
                        {{-- Read-only poster assessment --}}
                        <div class="p-5">
                            <div class="space-y-2">
                                @foreach($group->poster_assessment->details as $detail)
                                <div class="flex items-center justify-between py-1.5 border-b border-gray-50">
                                    <span class="text-xs text-gray-600 font-medium">{{ $detail->criterion }}</span>
                                    <span class="font-bold text-purple-600 text-sm">{{ $detail->score }}</span>
                                </div>
                                @endforeach
                                <div class="flex items-center justify-between pt-2 font-bold text-sm">
                                    <span class="text-xs text-gray-700">Weighted Total Score</span>
                                    <span class="text-purple-700 text-base font-extrabold">{{ number_format($group->poster_assessment->calculateWeightedScore(), 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- PANEL KANAN: PENILAIAN IPA --}}
                    <div>
                        <div class="px-5 py-3 bg-teal-50 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="ph ph-atom text-teal-600 text-lg"></i>
                                <span class="text-sm font-bold text-teal-700">Penilaian IPA</span>
                            </div>
                            @if($group->my_assessment?->isSubmitted())
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                                    <i class="ph ph-check-circle"></i> Submitted
                                </span>
                            @elseif($group->my_assessment)
                                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Draft</span>
                            @else
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-bold rounded-full">Belum</span>
                            @endif
                        </div>

                        @if(!$group->my_assessment?->isSubmitted())
                        <form method="POST" action="{{ route('guru.assessments.store') }}" class="p-5 assessment-form" id="science-form-{{ $group->id }}" data-group-id="{{ $group->id }}" data-type="science">
                            @csrf
                            <input type="hidden" name="group_id" value="{{ $group->id }}">
                            <input type="hidden" name="assessment_type" value="science">

                            @if($criteria->isEmpty())
                                <p class="text-sm text-gray-400 italic">Kriteria IPA belum dikonfigurasi.</p>
                            @else
                            <div class="flex items-center justify-between mb-3 bg-teal-50/50 p-2.5 rounded-xl border border-teal-100">
                                <span class="text-xs font-bold text-brand-teal-dark">Isi Nilai Kriteria IPA</span>
                                <button type="button" onclick="presetAllScores('science-form-{{ $group->id }}', 75)"
                                        class="px-2 py-1 bg-teal-100 hover:bg-teal-200 text-brand-teal font-semibold text-[11px] rounded-lg transition-colors">
                                    <i class="ph ph-magic-wand mr-0.5"></i> Set Semua Default (75)
                                </button>
                            </div>

                            <div class="space-y-3 mb-4">
                                @foreach($criteria as $criterion)
                                @php
                                    $existingScore = $group->my_assessment?->details?->firstWhere('criterion', $criterion->name)?->score;
                                    $defaultScore = $existingScore !== null ? $existingScore : 75;
                                @endphp
                                <div class="bg-gray-50/80 rounded-xl p-3 border border-gray-100 hover:border-teal-200 transition-colors criterion-card"
                                     data-weight="{{ $criterion->weight }}" data-max="{{ $criterion->max_score }}">
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-xs text-gray-800">{{ $criterion->name }}
                                                <span class="text-brand-teal font-bold ml-1">({{ $criterion->weight }}%)</span>
                                            </p>
                                            <p class="text-[11px] text-gray-400 mt-0.5 truncate">{{ Str::limit($criterion->description, 60) }}</p>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <input type="number" name="scores[{{ $criterion->name }}]"
                                                value="{{ (int)$defaultScore }}"
                                                min="0" max="{{ (int)$criterion->max_score }}" required
                                                oninput="calculateLiveScore('science-form-{{ $group->id }}')"
                                                class="score-input w-16 border border-gray-200 rounded-lg px-2 py-1 text-xs font-bold text-center text-brand-teal focus:border-brand-teal focus:ring-1 focus:ring-brand-teal-light outline-none">
                                            <span class="text-xs text-gray-400 font-medium">/{{ (int)$criterion->max_score }}</span>
                                        </div>
                                    </div>
                                    {{-- Quick Preset Buttons --}}
                                    <div class="flex items-center justify-between pt-1 border-t border-gray-100/60 mt-1.5">
                                        <span class="text-[10px] text-gray-400">Preset cepat:</span>
                                        <div class="flex items-center gap-1">
                                            @foreach([70, 75, 80, 85, 90, 100] as $preset)
                                            <button type="button" onclick="setScoreInput(this, {{ $preset }}, 'science-form-{{ $group->id }}')"
                                                    class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-white border border-gray-200 hover:bg-brand-teal hover:text-white hover:border-brand-teal transition-all text-gray-600">
                                                {{ $preset }}
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="text" name="notes[{{ $criterion->name }}]"
                                        value="{{ $group->my_assessment?->details?->firstWhere('criterion', $criterion->name)?->note }}"
                                        class="w-full border border-gray-200 rounded-lg px-2.5 py-1 text-xs text-gray-600 focus:border-brand-teal outline-none mt-2 bg-white"
                                        placeholder="Catatan opsional...">
                                </div>
                                @endforeach
                            </div>

                            {{-- Live Score Preview Card --}}
                            <div class="bg-gradient-to-r from-teal-600 to-emerald-600 rounded-xl p-3.5 text-white shadow-sm mb-4 flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] uppercase tracking-wider font-semibold opacity-90">Auto Grading (Prediksi Total)</p>
                                    <div class="flex items-baseline gap-2 mt-0.5">
                                        <span class="text-2xl font-extrabold live-total-score">75.00</span>
                                        <span class="text-xs opacity-80">/ 100</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm live-grade-badge">
                                        Baik (B)
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" name="action" value="draft"
                                    class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition-colors flex items-center gap-1">
                                    <i class="ph ph-floppy-disk text-sm"></i> Simpan Draft
                                </button>
                                <button type="button" onclick="confirmSubmit(this.closest('form'), 'IPA')"
                                    class="px-4 py-2 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl text-xs font-semibold transition-all shadow-sm flex items-center gap-1">
                                    <i class="ph ph-paper-plane-tilt text-sm"></i> Submit Penilaian
                                </button>
                            </div>
                            @endif
                        </form>
                        @else
                        {{-- Read-only science assessment --}}
                        <div class="p-5">
                            <div class="space-y-2">
                                @foreach($group->my_assessment->details as $detail)
                                <div class="flex items-center justify-between py-1.5 border-b border-gray-50">
                                    <span class="text-xs text-gray-600 font-medium">{{ $detail->criterion }}</span>
                                    <span class="font-bold text-brand-teal text-sm">{{ $detail->score }}</span>
                                </div>
                                @endforeach
                                <div class="flex items-center justify-between pt-2 font-bold text-sm">
                                    <span class="text-xs text-gray-700">Weighted Total Score</span>
                                    <span class="text-brand-teal text-base font-extrabold">{{ number_format($group->my_assessment->calculateWeightedScore(), 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Ringkasan penilaian juri --}}
                        @if($group->juri_assessments->count() > 0)
                        <div class="px-5 pb-4 pt-2 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-500 mb-2">Penilaian Juri ({{ $group->juri_assessments->count() }} submitted)</p>
                            @foreach($group->juri_assessments as $juriAssessment)
                            <div class="flex items-center justify-between py-1">
                                <span class="text-xs text-gray-500">{{ $juriAssessment->getAssessor()?->getDisplayName() }}</span>
                                <span class="text-xs font-bold text-amber-600">{{ number_format($juriAssessment->calculateWeightedScore(), 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bottom Navigation Bar --}}
            <div class="flex items-center justify-start bg-white rounded-2xl p-4 border border-gray-100 shadow-card">
                <button type="button" onclick="hideGroupAssessment()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-bold transition-all">
                    <i class="ph ph-arrow-left"></i> Kembali ke Daftar Kelompok
                </button>
            </div>

        </div>
        @endforeach
    </div>

</div>

{{-- Poster Revision Modal Form (Hidden) --}}
<form id="form-poster-revision" method="POST" action="" class="hidden">
    @csrf
    <input type="hidden" name="status" value="revision">
    <input type="hidden" name="note" id="poster-revision-note" value="">
</form>
@endsection

@push('scripts')
<script>
let currentActiveGroupId = null;

document.addEventListener('DOMContentLoaded', function () {
    // Run hash router on initial load
    handleHashChange();
});

// Real-time group filtering in card grid
function filterGroups() {
    const q = (document.getElementById('group-search').value || '').toLowerCase().trim();
    const classFilter = document.getElementById('group-class-filter').value;

    document.querySelectorAll('.group-card').forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const leader = card.getAttribute('data-leader') || '';
        const groupClass = card.getAttribute('data-class') || '';

        const matchesQuery = !q || name.includes(q) || leader.includes(q);
        const matchesClass = !classFilter || groupClass === classFilter;

        if (matchesQuery && matchesClass) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

function showGroupAssessment(groupId) {
    // Hide dashboard
    document.getElementById('guru-assessment-dashboard').classList.add('hidden');
    
    // Hide all detail panels
    document.querySelectorAll('.group-detail-panel').forEach(p => p.classList.add('hidden'));
    
    // Show container and active panel
    document.getElementById('group-assessment-details-container').classList.remove('hidden');
    const panel = document.getElementById(`group-detail-${groupId}`);
    if (panel) {
        panel.classList.remove('hidden');
    }
    
    currentActiveGroupId = groupId;
    window.location.hash = `group-${groupId}`;
    
    // Calculate live scores for initial view
    calculateLiveScore(`poster-form-${groupId}`);
    calculateLiveScore(`science-form-${groupId}`);
}

function hideGroupAssessment() {
    // Show dashboard
    document.getElementById('guru-assessment-dashboard').classList.remove('hidden');
    // Hide details container
    document.getElementById('group-assessment-details-container').classList.add('hidden');
    
    currentActiveGroupId = null;
    window.location.hash = '';
}

function handleHashChange() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#group-')) {
        const groupId = parseInt(hash.replace('#group-', ''));
        if (groupId) {
            showGroupAssessment(groupId);
        }
    } else {
        hideGroupAssessment();
    }
}

window.addEventListener('hashchange', handleHashChange);

function setScoreInput(btn, value, formId) {
    const card = btn.closest('.criterion-card');
    if (!card) return;
    const input = card.querySelector('.score-input');
    if (input) {
        input.value = value;
        calculateLiveScore(formId);
    }
}

function presetAllScores(formId, value) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.querySelectorAll('.score-input').forEach(input => {
        input.value = value;
    });
    calculateLiveScore(formId);
}

function calculateLiveScore(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    let totalWeightedScore = 0;
    const cards = form.querySelectorAll('.criterion-card');

    cards.forEach(card => {
        const weight = parseFloat(card.getAttribute('data-weight')) || 0;
        const maxScore = parseFloat(card.getAttribute('data-max')) || 100;
        const input = card.querySelector('.score-input');
        const rawScore = parseFloat(input?.value) || 0;

        const weighted = (rawScore / maxScore) * weight;
        totalWeightedScore += weighted;
    });

    const scoreDisplay = form.querySelector('.live-total-score');
    const badgeDisplay = form.querySelector('.live-grade-badge');

    if (scoreDisplay) {
        scoreDisplay.textContent = totalWeightedScore.toFixed(2);
    }

    if (badgeDisplay) {
        if (totalWeightedScore >= 85) {
            badgeDisplay.textContent = 'Sangat Baik (A)';
            badgeDisplay.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm live-grade-badge text-emerald-100';
        } else if (totalWeightedScore >= 75) {
            badgeDisplay.textContent = 'Baik (B)';
            badgeDisplay.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm live-grade-badge text-blue-100';
        } else if (totalWeightedScore >= 65) {
            badgeDisplay.textContent = 'Cukup (C)';
            badgeDisplay.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm live-grade-badge text-amber-100';
        } else {
            badgeDisplay.textContent = 'Perlu Bimbingan (D)';
            badgeDisplay.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-sm live-grade-badge text-red-100';
        }
    }
}

function requestPosterRevision(posterId, groupName) {
    Swal.fire({
        title: `Minta Revisi Poster ${groupName}?`,
        text: 'Masukkan alasan atau instruksi revisi untuk kelompok siswa:',
        input: 'textarea',
        inputPlaceholder: 'Contoh: Tulisan terlalu kecil / Format tidak jelas...',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        confirmButtonText: 'Kirim Revisi',
        cancelButtonText: 'Batal',
    }).then(r => {
        if (r.isConfirmed) {
            const form = document.getElementById('form-poster-revision');
            form.action = `/guru/posters/${posterId}/status`;
            document.getElementById('poster-revision-note').value = r.value || '';
            form.submit();
        }
    });
}

function confirmSubmit(form, label) {
    Swal.fire({
        title: `Submit Penilaian ${label}?`,
        text: 'Penilaian yang sudah disubmit tidak dapat diubah kembali.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0d9488',
        confirmButtonText: 'Ya, Submit',
        cancelButtonText: 'Batal',
    }).then(r => {
        if (r.isConfirmed) {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'action'; input.value = 'submit';
            form.appendChild(input);
            form.submit();
        }
    });
}
</script>
@endpush
