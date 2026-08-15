@extends('layouts.app')

@section('title', 'Penilaian IPA & Poster')
@section('page-title', 'Penilaian Kelompok')

@section('nav-menu')
    <a href="{{ route('guru.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('guru.groups.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-users text-xl"></i> Manajemen Kelompok
    </a>
    <a href="{{ route('guru.assessments.index') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-clipboard-text text-xl"></i> Penilaian
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    @forelse($groups as $group)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">

        {{-- Header Kelompok --}}
        <div class="p-5 border-b border-gray-100 flex items-center justify-between gap-3">
            <div>
                <h4 class="font-bold text-gray-800 text-base">{{ $group->name }}</h4>
                <p class="text-sm text-gray-500 mt-0.5">{{ $group->members->count() }} Anggota
                    @if($group->productMetadata)
                        · <span class="text-brand-teal font-medium">{{ $group->productMetadata->product_name }}</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('guru.scores.show', $group->id) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold transition-colors">
                <i class="ph ph-chart-bar"></i> Rekap Nilai
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">

            {{-- ══════════════════════════════════════════
                 PANEL KIRI: PENILAIAN POSTER
            ══════════════════════════════════════════ --}}
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
                <div class="px-5 py-2 bg-purple-50/30 border-b border-gray-100 flex items-center gap-2">
                    <i class="ph ph-file text-purple-400 text-sm"></i>
                    <a href="{{ Storage::disk('public')->url($group->poster->file_path) }}" target="_blank"
                       class="text-xs text-purple-600 hover:underline">{{ $group->poster->original_name }}</a>
                </div>
                @else
                <div class="px-5 py-2 bg-gray-50 border-b border-gray-100 text-xs text-gray-400 flex items-center gap-2">
                    <i class="ph ph-warning text-amber-400"></i> Poster belum diupload
                </div>
                @endif

                @if(!$group->poster_assessment?->isSubmitted())
                <form method="POST" action="{{ route('guru.assessments.store') }}" class="p-5">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <input type="hidden" name="assessment_type" value="poster">

                    @if($posterCriteria->isEmpty())
                        <p class="text-sm text-gray-400 italic">Kriteria poster belum dikonfigurasi.</p>
                    @else
                    <div class="space-y-3 mb-4">
                        @foreach($posterCriteria as $criterion)
                        <div class="bg-gray-50 rounded-xl p-3">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-xs text-gray-800">{{ $criterion->name }}
                                        <span class="text-gray-400 font-normal">(bobot {{ $criterion->weight }}%)</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <input type="number" name="scores[{{ $criterion->name }}]"
                                        value="{{ $group->poster_assessment?->details?->firstWhere('criterion', $criterion->name)?->score ?? '' }}"
                                        min="0" max="{{ $criterion->max_score }}" required
                                        class="w-16 border border-gray-200 rounded-lg px-2 py-1 text-xs text-center focus:border-purple-400 outline-none">
                                    <span class="text-xs text-gray-400">/{{ (int)$criterion->max_score }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" name="action" value="draft"
                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                            <i class="ph ph-floppy-disk mr-1"></i> Simpan Draft
                        </button>
                        <button type="button" onclick="confirmSubmit(this.closest('form'), 'Poster')"
                            class="px-4 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold transition-colors">
                            <i class="ph ph-paper-plane-tilt mr-1"></i> Submit
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
                            <span class="text-xs text-gray-600">{{ $detail->criterion }}</span>
                            <span class="font-bold text-purple-600 text-sm">{{ $detail->score }}</span>
                        </div>
                        @endforeach
                        <div class="flex items-center justify-between pt-1.5 font-bold">
                            <span class="text-xs text-gray-700">Weighted Score</span>
                            <span class="text-purple-700">{{ number_format($group->poster_assessment->calculateWeightedScore(), 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- ══════════════════════════════════════════
                 PANEL KANAN: PENILAIAN IPA
            ══════════════════════════════════════════ --}}
            <div>
                <div class="px-5 py-3 bg-teal-50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-atom text-teal-600"></i>
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
                <form method="POST" action="{{ route('guru.assessments.store') }}" class="p-5">
                    @csrf
                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                    <input type="hidden" name="assessment_type" value="science">

                    @if($criteria->isEmpty())
                        <p class="text-sm text-gray-400 italic">Kriteria IPA belum dikonfigurasi.</p>
                    @else
                    <div class="space-y-3 mb-4">
                        @foreach($criteria as $criterion)
                        <div class="bg-gray-50 rounded-xl p-3">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-xs text-gray-800">{{ $criterion->name }}
                                        <span class="text-gray-400 font-normal">({{ $criterion->weight }}%)</span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ Str::limit($criterion->description, 60) }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <input type="number" name="scores[{{ $criterion->name }}]"
                                        value="{{ $group->my_assessment?->details?->firstWhere('criterion', $criterion->name)?->score ?? '' }}"
                                        min="0" max="{{ $criterion->max_score }}" required
                                        class="w-16 border border-gray-200 rounded-lg px-2 py-1 text-xs text-center focus:border-brand-teal outline-none">
                                    <span class="text-xs text-gray-400">/{{ (int)$criterion->max_score }}</span>
                                </div>
                            </div>
                            <input type="text" name="notes[{{ $criterion->name }}]"
                                value="{{ $group->my_assessment?->details?->firstWhere('criterion', $criterion->name)?->note }}"
                                class="w-full border border-gray-200 rounded-lg px-2 py-1 text-xs text-gray-600 focus:border-brand-teal outline-none mt-1"
                                placeholder="Catatan (opsional)...">
                        </div>
                        @endforeach
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" name="action" value="draft"
                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition-colors">
                            <i class="ph ph-floppy-disk mr-1"></i> Simpan Draft
                        </button>
                        <button type="button" onclick="confirmSubmit(this.closest('form'), 'IPA')"
                            class="px-4 py-1.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-lg text-xs font-semibold transition-colors">
                            <i class="ph ph-paper-plane-tilt mr-1"></i> Submit
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
                            <span class="text-xs text-gray-600">{{ $detail->criterion }}</span>
                            <span class="font-bold text-brand-teal text-sm">{{ $detail->score }}</span>
                        </div>
                        @endforeach
                        <div class="flex items-center justify-between pt-1.5 font-bold">
                            <span class="text-xs text-gray-700">Weighted Score</span>
                            <span class="text-brand-teal">{{ number_format($group->my_assessment->calculateWeightedScore(), 2) }}</span>
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
    @empty
    <div class="bg-white rounded-2xl p-12 border border-gray-100 text-center text-gray-400 shadow-card">
        <i class="ph ph-clipboard-text text-4xl mb-3 block opacity-40"></i>
        <p>Belum ada kelompok untuk dinilai.</p>
        <a href="{{ route('guru.groups.index') }}" class="text-brand-teal text-sm mt-2 inline-block hover:underline">Buat kelompok dulu →</a>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
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
