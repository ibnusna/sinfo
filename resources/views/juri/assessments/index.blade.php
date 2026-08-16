@extends('layouts.app')

@section('title', 'Penilaian Juri — Semua Kelompok')
@section('page-title', 'Penilaian Juri')

@section('nav-menu')
    <a href="{{ route('juri.dashboard') }}" class="nav-link {{ request()->routeIs('juri.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('juri.assessments.index') }}" class="nav-link {{ request()->routeIs('juri.assessments.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-star text-xl"></i> Penilaian Juri
    </a>
    <a href="{{ route('panduan.show') }}" target="_blank" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2 border-t border-gray-100">
        <i class="ph ph-book-bookmark text-xl text-brand-teal"></i> Panduan Projek IPA
    </a>
@endsection

@section('content')
<div class="space-y-4 slide-up">

    {{-- Filter & Search Bar --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-card flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Penilaian Kelompok Makanan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Filter dan pilih kelompok yang akan dinilai oleh juri</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:flex-none md:w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="juri-group-search" onkeyup="filterJuriGroups()"
                       placeholder="Cari kelompok / ketua..."
                       class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2 text-xs focus:border-brand-teal outline-none transition-all">
            </div>
            <div class="w-full md:w-48">
                <select id="juri-class-filter" onchange="filterJuriGroups()"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs focus:border-brand-teal outline-none transition-all text-gray-600 font-medium bg-white">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Groups List --}}
    <div class="space-y-4" id="juri-groups-list">
        @forelse($groups as $group)
        <div class="juri-group-card bg-white rounded-2xl border border-gray-100 shadow-card p-5 flex items-center justify-between hover:shadow-soft transition-shadow"
             data-name="{{ strtolower($group->name) }}"
             data-leader="{{ strtolower($group->leader_data?->getDisplayName() ?? '') }}"
             data-class="{{ $group->leader_data?->siswa?->kelas?->nama_kelas ?? 'Tanpa Kelas' }}">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center">
                    <i class="ph ph-users text-2xl text-yellow-500"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="font-bold text-gray-800 text-sm sm:text-base">{{ $group->name }}</h4>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-amber-50 text-amber-700">
                            {{ $group->leader_data?->siswa?->kelas?->nama_kelas ?? '-' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Ketua: <span class="font-medium text-gray-700">{{ $group->leader_data?->getDisplayName() ?? '-' }}</span></p>
                    @if($group->poster)
                        <span class="text-[11px] text-purple-600 font-medium flex items-center gap-1 mt-1">
                            <i class="ph ph-image"></i> Poster tersedia
                        </span>
                    @else
                        <span class="text-[11px] text-gray-400 flex items-center gap-1 mt-1">
                            <i class="ph ph-image"></i> Belum ada poster
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($group->my_assessment?->isSubmitted())
                    <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-200">Submitted ✓</span>
                @elseif($group->my_assessment)
                    <span class="px-3 py-1 bg-yellow-50 text-yellow-700 text-xs font-bold rounded-full border border-yellow-200">Draft</span>
                @else
                    <span class="px-3 py-1 bg-gray-50 text-gray-500 text-xs font-bold rounded-full border border-gray-200">Belum Dinilai</span>
                @endif
                <a href="{{ route('juri.assessments.show', $group->id) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-teal text-white rounded-xl text-xs font-semibold hover:bg-brand-teal-dark transition-colors shadow-sm">
                    @if($group->my_assessment?->isSubmitted())
                        <i class="ph ph-eye text-sm"></i> Lihat
                    @else
                        <i class="ph ph-pencil-simple text-sm"></i> Nilai
                    @endif
                </a>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-12 border border-gray-100 text-center text-gray-400 shadow-card">
            <i class="ph ph-users text-4xl mb-3 block opacity-40"></i>
            <p class="font-medium">Belum ada kelompok dalam program ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterJuriGroups() {
    const q = (document.getElementById('juri-group-search').value || '').toLowerCase().trim();
    const classFilter = document.getElementById('juri-class-filter').value;

    document.querySelectorAll('.juri-group-card').forEach(card => {
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
</script>
@endpush
