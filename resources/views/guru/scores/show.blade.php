@extends('layouts.app')

@section('title', 'Rekap Nilai Kelompok')
@section('page-title', 'Rekap Nilai Kelompok')

@section('nav-menu')
    <a href="{{ route('guru.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('guru.groups.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-users text-xl"></i> Manajemen Kelompok
    </a>
    <a href="{{ route('guru.assessments.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-clipboard-text text-xl"></i> Penilaian
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Back Button & Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('guru.assessments.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-brand-teal font-medium transition-colors">
            <i class="ph ph-arrow-left"></i> Kembali ke Penilaian
        </a>
        @if($isFinalized)
            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1.5">
                <i class="ph ph-lock-key"></i> Finalized
            </span>
        @else
            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full flex items-center gap-1.5">
                <i class="ph ph-spinner"></i> Sedang Berjalan
            </span>
        @endif
    </div>

    {{-- Group Info Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $group->name }}</h2>
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                    <span class="flex items-center gap-1"><i class="ph ph-users"></i> {{ $group->members->count() }} Anggota</span>
                    @if($group->productMetadata)
                        <span class="flex items-center gap-1 text-brand-teal font-medium">
                            <i class="ph ph-package"></i> {{ $group->productMetadata->product_name }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 mb-1">Status Penilaian</p>
                @if($isFinalized)
                    <p class="font-semibold text-green-600 flex items-center gap-1 justify-end"><i class="ph ph-check-circle"></i> Selesai</p>
                @else
                    <p class="font-semibold text-amber-600 flex items-center gap-1 justify-end"><i class="ph ph-hourglass-high"></i> Menunggu Finalisasi</p>
                @endif
            </div>
        </div>
    </div>

    @if(!$scorePreview['weights_configured'])
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-yellow-700 text-sm flex items-start gap-3">
        <i class="ph ph-warning-circle text-xl shrink-0 mt-0.5"></i>
        <div>
            <strong class="block mb-1">Peringatan: Bobot Kriteria Belum Dikonfigurasi</strong>
            <p>Admin belum mengatur persentase bobot untuk Poster, IPA, dan Makanan. Sistem saat ini menggunakan bobot default (rata rata) untuk perhitungan sementara.</p>
        </div>
    </div>
    @endif

    {{-- Score Preview Panel --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Poster Score --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                    <i class="ph ph-image"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Poster</p>
                    <p class="text-[10px] text-gray-400">Bobot: {{ $scorePreview['poster_weight'] ?? 0 }}%</p>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">
                {{ $scorePreview['poster_score'] !== null ? number_format($scorePreview['poster_score'], 2) : '-' }}
            </p>
        </div>

        {{-- Science Score --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center">
                    <i class="ph ph-atom"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">IPA</p>
                    <p class="text-[10px] text-gray-400">Bobot: {{ $scorePreview['science_weight'] ?? 0 }}%</p>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">
                {{ $scorePreview['science_score'] !== null ? number_format($scorePreview['science_score'], 2) : '-' }}
            </p>
        </div>

        {{-- Food Score --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i class="ph ph-pizza"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Makanan</p>
                    <p class="text-[10px] text-gray-400">Bobot: {{ $scorePreview['food_weight'] ?? 0 }}%</p>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-800">
                {{ $scorePreview['food_score'] !== null ? number_format($scorePreview['food_score'], 2) : '-' }}
            </p>
        </div>

        {{-- Group Score --}}
        <div class="bg-indigo-50 rounded-2xl border border-indigo-100 shadow-sm p-5 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <i class="ph ph-chart-line-up text-9xl text-indigo-600"></i>
            </div>
            <div class="relative z-10">
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2">Nilai Akhir Kelompok</p>
                <p class="text-4xl font-black text-indigo-900">
                    {{ $scorePreview['score'] !== null ? number_format($scorePreview['score'], 2) : '-' }}
                </p>
                <p class="text-xs text-indigo-500 mt-2">
                    @if($isFinalized) Nilai telah dikunci @else Estimasi nilai @endif
                </p>
            </div>
        </div>
    </div>

    {{-- Individual Scores Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">Nilai Individu Anggota</h3>
            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-500 rounded-md flex items-center gap-1">
                <i class="ph ph-lock-key"></i> Data ini hanya terlihat oleh Guru
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 font-semibold whitespace-nowrap">Nama Anggota</th>
                        <th class="px-6 py-3 font-semibold whitespace-nowrap">Status Kontribusi</th>
                        <th class="px-6 py-3 font-semibold whitespace-nowrap text-center">Faktor Pengali</th>
                        <th class="px-6 py-3 font-semibold whitespace-nowrap text-right">Nilai Individu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($memberScores as $ms)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $ms->name }}</td>
                        <td class="px-6 py-4">
                            @if($ms->is_contributed)
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Berkontribusi</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Tidak Berkontribusi</span>
                            @endif

                            @if($ms->reason)
                                <div class="mt-2 text-xs text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-100 flex gap-1.5 items-start">
                                    <i class="ph ph-info mt-0.5 shrink-0"></i>
                                    <span class="italic">"{{ $ms->reason }}"</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($ms->factor !== null)
                                <span class="font-mono bg-gray-100 px-2 py-1 rounded text-gray-700 text-xs">{{ $ms->factor }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum dikonfigurasi</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($ms->individual_score !== null)
                                <span class="text-lg font-bold text-gray-800">{{ number_format($ms->individual_score, 2) }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">Belum dapat dihitung</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Assessment Details Accordion --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Rincian Penilaian</h3>
        </div>
        <div class="divide-y divide-gray-100">
            {{-- Poster Details --}}
            <details class="group">
                <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 font-medium text-gray-700 flex items-center justify-between transition-colors">
                    <span class="flex items-center gap-2"><i class="ph ph-image text-purple-500"></i> Penilaian Poster</span>
                    <i class="ph ph-caret-down group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="px-6 py-4 bg-gray-50/50">
                    @if($posterAssessment && $posterAssessment->isSubmitted())
                        <div class="space-y-3">
                            @foreach($posterAssessment->details as $detail)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">{{ $detail->criterion }}</span>
                                    <span class="font-semibold text-gray-800">{{ $detail->score }}</span>
                                </div>
                            @endforeach
                            <div class="pt-3 border-t border-gray-200 flex justify-between items-center text-sm font-bold">
                                <span class="text-gray-700">Total Terbobot</span>
                                <span class="text-purple-600">{{ number_format($posterAssessment->calculateWeightedScore(), 2) }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">Belum ada penilaian poster yang disubmit.</p>
                    @endif
                </div>
            </details>

            {{-- Science Details --}}
            <details class="group">
                <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 font-medium text-gray-700 flex items-center justify-between transition-colors">
                    <span class="flex items-center gap-2"><i class="ph ph-atom text-teal-500"></i> Penilaian IPA</span>
                    <i class="ph ph-caret-down group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="px-6 py-4 bg-gray-50/50">
                    @if($scienceAssessment && $scienceAssessment->isSubmitted())
                        <div class="space-y-3">
                            @foreach($scienceAssessment->details as $detail)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">{{ $detail->criterion }}</span>
                                    <span class="font-semibold text-gray-800">{{ $detail->score }}</span>
                                </div>
                            @endforeach
                            <div class="pt-3 border-t border-gray-200 flex justify-between items-center text-sm font-bold">
                                <span class="text-gray-700">Total Terbobot</span>
                                <span class="text-teal-600">{{ number_format($scienceAssessment->calculateWeightedScore(), 2) }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">Belum ada penilaian IPA yang disubmit.</p>
                    @endif
                </div>
            </details>

            {{-- Food Details --}}
            <details class="group">
                <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 font-medium text-gray-700 flex items-center justify-between transition-colors">
                    <span class="flex items-center gap-2"><i class="ph ph-pizza text-orange-500"></i> Penilaian Makanan (Juri)</span>
                    <i class="ph ph-caret-down group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="px-6 py-4 bg-gray-50/50">
                    @if($foodAssessments->count() > 0)
                        <div class="space-y-6">
                            @foreach($foodAssessments as $fa)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 pb-1 border-b border-gray-200 flex items-center justify-between">
                                        <span>{{ $fa->assessor_name ?? 'Juri' }}</span>
                                        <span class="text-orange-600">{{ number_format($fa->calculateWeightedScore(), 2) }}</span>
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($fa->details as $detail)
                                            <div class="flex justify-between items-center text-xs">
                                                <span class="text-gray-600">{{ $detail->criterion }}</span>
                                                <span class="font-medium text-gray-800">{{ $detail->score }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">Belum ada penilaian makanan dari juri.</p>
                    @endif
                </div>
            </details>
        </div>
    </div>

    {{-- Finalization Section --}}
    @if(!$isFinalized)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ph ph-check-square-offset text-xl text-brand-teal"></i> Finalisasi Nilai
            </h3>

            @if($finalizationCheck['ok'])
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 text-sm text-green-700">
                    <p class="flex items-center gap-2 font-medium mb-1"><i class="ph ph-check-circle text-lg"></i> Semua persyaratan terpenuhi!</p>
                    <p>Anda dapat mengunci nilai kelompok ini. Setelah dikunci, nilai tidak dapat diubah lagi dan akan dipublikasikan.</p>
                </div>
                
                <form id="form-finalize" method="POST" action="{{ route('guru.groups.finalize', $group->id) }}" class="flex justify-end">
                    @csrf
                    <button type="button" onclick="confirmFinalize()" class="px-6 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl font-semibold shadow-sm transition-colors flex items-center gap-2">
                        <i class="ph ph-lock-key"></i> Kunci Nilai
                    </button>
                </form>
            @else
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4 text-sm text-red-700">
                    <p class="flex items-center gap-2 font-medium mb-2"><i class="ph ph-warning-circle text-lg"></i> Tidak dapat melakukan finalisasi</p>
                    <p class="mb-2">Beberapa persyaratan belum terpenuhi:</p>
                    <ul class="list-disc list-inside space-y-1 ml-2">
                        @foreach($finalizationCheck['missing'] as $missing)
                            <li>{{ $missing }}</li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="flex justify-end">
                    <button disabled class="px-6 py-2.5 bg-gray-300 text-gray-500 rounded-xl font-semibold cursor-not-allowed flex items-center gap-2">
                        <i class="ph ph-lock-key"></i> Kunci Nilai
                    </button>
                </div>
            @endif
        </div>
    @else
        <div class="bg-green-50 rounded-2xl border border-green-200 p-6 flex items-center justify-between">
            <div class="flex items-start gap-3">
                <i class="ph ph-check-circle text-2xl text-green-600 shrink-0"></i>
                <div>
                    <h3 class="font-bold text-green-800">Nilai Telah Dikunci</h3>
                    <p class="text-sm text-green-700 mt-1">
                        Nilai kelompok ini telah difinalisasi pada <strong>{{ $finalScores->first()?->finalized_at?->format('d M Y, H:i') ?? 'Waktu tidak diketahui' }}</strong>.
                    </p>
                </div>
            </div>
            <span class="px-4 py-2 bg-white text-green-700 rounded-xl font-bold border border-green-200 shadow-sm">FINAL</span>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function confirmFinalize() {
    Swal.fire({
        title: 'Kunci Nilai Kelompok?',
        text: 'Setelah difinalisasi, nilai ini akan dikunci, tidak dapat diubah lagi, dan dapat dilihat oleh siswa maupun admin.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0d9488', // brand-teal
        confirmButtonText: 'Ya, Kunci Nilai',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('form-finalize').submit();
        }
    });
}
</script>
@endpush
