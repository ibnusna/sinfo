@extends('layouts.app')

@section('title', 'Penilaian Juri — ' . $group->name)
@section('page-title', 'Penilaian: ' . $group->name)

@section('nav-menu')
    <a href="{{ route('juri.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('juri.assessments.index') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-star text-xl"></i> Penilaian Juri
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Group Info --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-800">{{ $group->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">Ketua: {{ $group->leader_data?->getDisplayName() ?? '-' }}</p>
                <p class="text-sm text-gray-500">{{ $group->members->count() }} Anggota</p>
            </div>
            @if($assessment?->isSubmitted())
                <span class="px-3 py-1.5 bg-green-100 text-green-700 text-sm font-bold rounded-full flex items-center gap-1 shrink-0">
                    <i class="ph ph-lock-key"></i> Submitted & Terkunci
                </span>
            @endif
        </div>
    </div>

    {{-- Metadata Produk (referensi untuk juri) --}}
    @if($productMetadata)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="px-5 py-3 bg-amber-50 border-b border-gray-100 flex items-center gap-2">
            <i class="ph ph-cooking-pot text-amber-600"></i>
            <span class="text-sm font-bold text-amber-700">Informasi Produk (Referensi)</span>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama Produk</p>
                <p class="font-bold text-gray-800">{{ $productMetadata->product_name }}</p>
            </div>
            @if($productMetadata->description)
            <div class="md:col-span-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Deskripsi</p>
                <p class="text-sm text-gray-700">{{ $productMetadata->description }}</p>
            </div>
            @endif
            @if($productMetadata->ingredients)
            <div class="md:col-span-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Bahan-Bahan</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $productMetadata->ingredients }}</p>
            </div>
            @endif
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Kandungan Gizi</p>
                <div class="space-y-1 text-sm">
                    @if($productMetadata->carbohydrate)
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 shrink-0">Karbohidrat:</span>
                        <span class="text-gray-700">{{ $productMetadata->carbohydrate }}</span>
                    </div>
                    @endif
                    @if($productMetadata->protein)
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 shrink-0">Protein:</span>
                        <span class="text-gray-700">{{ $productMetadata->protein }}</span>
                    </div>
                    @endif
                    @if($productMetadata->fat)
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 shrink-0">Lemak:</span>
                        <span class="text-gray-700">{{ $productMetadata->fat }}</span>
                    </div>
                    @endif
                    @if($productMetadata->other_nutrients)
                    <div class="flex items-start gap-2">
                        <span class="text-gray-400 shrink-0">Lainnya:</span>
                        <span class="text-gray-700">{{ $productMetadata->other_nutrients }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Poster --}}
    @if($group->poster)
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card">
        <h4 class="font-bold text-gray-700 mb-3 text-sm">Poster Kelompok</h4>
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center shrink-0">
                <i class="ph ph-image text-xl text-purple-500"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 text-sm truncate">{{ $group->poster->original_name }}</p>
                <p class="text-xs text-gray-400">{{ $group->poster->getFileSizeFormatted() }}</p>
            </div>
            <a href="{{ $group->poster->getPublicUrl() }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl text-sm font-medium transition-colors shrink-0">
                <i class="ph ph-eye"></i> Lihat
            </a>
        </div>
    </div>
    @endif

    {{-- Assessment Form / Read-Only --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <i class="ph ph-clipboard-text text-brand-teal"></i>
            <h4 class="font-bold text-gray-800 text-sm">Form Penilaian Makanan</h4>
        </div>

        @if($assessment?->isSubmitted())
        {{-- Read-only mode --}}
        <div class="p-5">
            <div class="bg-green-50 border border-green-200 rounded-xl p-3 mb-4 text-green-700 text-xs flex items-center gap-2">
                <i class="ph ph-lock-key"></i>
                Penilaian disubmit pada {{ $assessment->submitted_at?->format('d M Y H:i') }} — terkunci.
            </div>
            <div class="space-y-3">
                @foreach($assessment->details as $detail)
                <div class="flex items-center justify-between py-2.5 border-b border-gray-50">
                    <div class="flex-1 min-w-0 mr-4">
                        <p class="font-semibold text-sm text-gray-700">{{ $detail->criterion }}</p>
                        @if($detail->note)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $detail->note }}</p>
                        @endif
                    </div>
                    <span class="font-bold text-xl text-brand-teal shrink-0">{{ $detail->score }}</span>
                </div>
                @endforeach
                <div class="flex items-center justify-between pt-2 font-bold text-base">
                    <span class="text-gray-700">Weighted Score (Food)</span>
                    <span class="text-brand-teal text-lg">{{ number_format($assessment->calculateWeightedScore(), 2) }}</span>
                </div>
            </div>
        </div>

        @else
        {{-- Editable form --}}
        <form method="POST" action="{{ route('juri.assessments.store', $group->id) }}" class="p-5 space-y-4" id="food-assessment-form">
            @csrf

            @if($criteria->isEmpty())
                <div class="text-center py-8 text-gray-400">
                    <i class="ph ph-warning text-3xl mb-2 block"></i>
                    <p class="text-sm">Kriteria penilaian makanan belum dikonfigurasi oleh operator.</p>
                </div>
            @else
            @foreach($criteria as $criterion)
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-gray-800">
                            {{ $criterion->name }}
                            <span class="text-gray-400 font-normal text-xs ml-1">(bobot {{ $criterion->weight }}%)</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $criterion->description }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <input type="number" name="scores[{{ $criterion->name }}]"
                            value="{{ $assessment?->details?->firstWhere('criterion', $criterion->name)?->score ?? '' }}"
                            min="0" max="{{ $criterion->max_score }}" step="0.01" required
                            class="w-20 border border-gray-200 rounded-lg px-3 py-2 text-sm text-center font-bold focus:border-brand-teal outline-none">
                        <span class="text-xs text-gray-400">/{{ (int)$criterion->max_score }}</span>
                    </div>
                </div>
                <input type="text" name="notes[{{ $criterion->name }}]"
                    value="{{ $assessment?->details?->firstWhere('criterion', $criterion->name)?->note }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-600 focus:border-brand-teal outline-none"
                    placeholder="Catatan (opsional)...">
            </div>
            @endforeach

            <div class="flex justify-between items-center pt-2">
                <a href="{{ route('juri.assessments.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-brand-teal transition-colors">
                    <i class="ph ph-arrow-left"></i> Kembali
                </a>
                <div class="flex gap-3">
                    <button type="submit" name="action" value="draft"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">
                        <i class="ph ph-floppy-disk mr-1"></i> Simpan Draft
                    </button>
                    <button type="button" onclick="confirmSubmitJuri()"
                        class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold transition-colors">
                        <i class="ph ph-paper-plane-tilt mr-1"></i> Submit & Kunci
                    </button>
                </div>
            </div>
            @endif
        </form>
        @endif
    </div>

    @if(!$assessment?->isSubmitted())
    <div class="flex">
        <a href="{{ route('juri.assessments.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-brand-teal transition-colors">
            <i class="ph ph-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function confirmSubmitJuri() {
    Swal.fire({
        title: 'Submit & Kunci Penilaian?',
        text: 'Penilaian yang sudah disubmit TIDAK DAPAT diubah kembali.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        confirmButtonText: 'Ya, Submit & Kunci',
        cancelButtonText: 'Batal',
    }).then(r => {
        if (r.isConfirmed) {
            const form = document.getElementById('food-assessment-form');
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'action'; input.value = 'submit';
            form.appendChild(input);
            form.submit();
        }
    });
}
</script>
@endpush
