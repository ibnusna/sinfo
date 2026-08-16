@extends('layouts.app')

@section('title', 'Panduan Projek IPA')
@section('page-title', 'Dokumentasi & Panduan Projek')

@push('styles')
<style>
/* Modern Markdown Render Styles */
.markdown-body {
    word-break: break-word;
    overflow-wrap: break-word;
}
.markdown-body h1 {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f766e;
    border-bottom: 2px solid #ccfbf1;
    padding-bottom: 0.5rem;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}
.markdown-body h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1f2937;
    margin-top: 1.75rem;
    margin-bottom: 0.75rem;
}
.markdown-body h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}
.markdown-body p {
    margin-bottom: 1rem;
    line-height: 1.7;
    color: #4b5563;
}
.markdown-body ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
    color: #4b5563;
}
.markdown-body ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1rem;
    color: #4b5563;
}
.markdown-body li {
    margin-bottom: 0.4rem;
}
.markdown-body table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
    margin-bottom: 1.5rem;
    border-radius: 0.75rem;
    overflow: hidden;
    display: block;
    overflow-x: auto;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.markdown-body th {
    background-color: #0d9488;
    color: #ffffff;
    font-weight: 600;
    text-align: left;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
}
.markdown-body td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.875rem;
    color: #374151;
}
.markdown-body tr:nth-child(even) td {
    background-color: #f9fafb;
}
.markdown-body blockquote {
    border-left: 4px solid #facc15;
    background-color: #fefce8;
    padding: 0.75rem 1rem;
    border-radius: 0 0.75rem 0.75rem 0;
    margin: 1rem 0;
    color: #854d0e;
    font-style: italic;
}
.markdown-body code {
    background-color: #f1f5f9;
    color: #0f766e;
    padding: 0.15rem 0.4rem;
    border-radius: 0.375rem;
    font-size: 0.85em;
}
.markdown-body pre {
    background-color: #1e293b;
    color: #f8fafc;
    padding: 1rem;
    border-radius: 0.75rem;
    overflow-x: auto;
    margin: 1rem 0;
}
.markdown-body img {
    max-width: 100%;
    height: auto;
    border-radius: 0.75rem;
}
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $role = $user ? $user->getScfRole() : 'guest';
@endphp
<div class="space-y-6 slide-up">

    {{-- Header Banner --}}
    <div class="bg-gradient-to-r from-brand-teal to-teal-800 text-white rounded-2xl p-5 sm:p-8 shadow-soft flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
        <div class="relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-teal-100 mb-3">
                <i class="ph-fill ph-book-open"></i> Master Modul Projek IPA
            </div>
            <h2 class="text-xl sm:text-3xl font-black text-white tracking-tight">Panduan Pelaksanaan Science Food Festival</h2>
            <p class="text-teal-100 text-xs sm:text-sm mt-1 max-w-2xl">Petunjuk Teknis (Juknis), Petunjuk Pelaksanaan (Juklak), Rubrik Penilaian, dan Job Desk Anggota Kelompok.</p>
        </div>

        <div class="flex items-center gap-3 relative z-10 shrink-0 w-full sm:w-auto justify-end">
            @if(in_array($role, ['operator', 'super_admin']))
                <a href="{{ route('operator.markdown.edit', $cleanFilename) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-brand-dark rounded-xl font-bold text-xs transition-colors shadow-md">
                    <i class="ph-bold ph-pencil-simple text-base"></i> Edit Markdown
                </a>
            @endif
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-semibold text-xs transition-colors border border-white/20">
                <i class="ph ph-printer text-base"></i> Cetak / PDF
            </button>
        </div>
    </div>

    {{-- Mobile Table of Contents Accordion (visible on mobile only) --}}
    <details class="lg:hidden bg-white rounded-2xl border border-gray-100 shadow-card p-4 group">
        <summary class="font-bold text-xs uppercase tracking-wider text-gray-800 flex items-center justify-between cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="ph ph-list-bullets text-brand-teal text-lg"></i>
                Daftar Isi / Navigasi Dokumen
            </span>
            <i class="ph ph-caret-down text-brand-teal text-base group-open:rotate-180 transition-transform"></i>
        </summary>
        <div class="mt-4 pt-3 border-t border-gray-100 space-y-2">
            @if(count($availableFiles) > 1)
                <div class="mb-3">
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase mb-1">Pilih Dokumen</label>
                    <select onchange="location.href = '/panduan/' + this.value" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-700 focus:border-brand-teal outline-none bg-gray-50">
                        @foreach($availableFiles as $fname => $label)
                            <option value="{{ $fname }}" {{ $cleanFilename === $fname ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <nav class="space-y-1 text-xs max-h-60 overflow-y-auto">
                @forelse($toc as $item)
                    <a href="#{{ $item['id'] }}" onclick="this.closest('details').removeAttribute('open')"
                       class="block py-1.5 px-2 rounded-lg transition-colors hover:bg-brand-teal-light hover:text-brand-teal truncate {{ $item['level'] === 1 ? 'font-bold text-gray-800' : ($item['level'] === 2 ? 'font-semibold text-gray-700 pl-3' : 'text-gray-500 pl-5') }}">
                        {{ $item['title'] }}
                    </a>
                @empty
                    <p class="text-gray-400 text-xs italic">Tidak ada sub-header detected.</p>
                @endforelse
            </nav>
        </div>
    </details>

    {{-- Main Grid: Desktop Table of Contents + Markdown Content --}}
    <div class="grid lg:grid-cols-4 gap-6 items-start">

        {{-- Desktop Sticky Sidebar (visible on lg screens only) --}}
        <div class="hidden lg:block lg:col-span-1 bg-white rounded-2xl p-5 border border-gray-100 shadow-card sticky top-6 max-h-[85vh] overflow-y-auto no-scrollbar">
            <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider mb-4 flex items-center gap-2 pb-2 border-b border-gray-100">
                <i class="ph ph-list-bullets text-brand-teal text-lg"></i> Daftar Isi / Navigasi
            </h4>

            @if(count($availableFiles) > 1)
                <div class="mb-4">
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase mb-1">Pilih Dokumen</label>
                    <select onchange="location.href = '/panduan/' + this.value" class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-xs text-gray-700 focus:border-brand-teal outline-none bg-gray-50">
                        @foreach($availableFiles as $fname => $label)
                            <option value="{{ $fname }}" {{ $cleanFilename === $fname ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <nav class="space-y-1 text-xs">
                @forelse($toc as $item)
                    <a href="#{{ $item['id'] }}" 
                       class="block py-1.5 px-2.5 rounded-lg transition-colors hover:bg-brand-teal-light hover:text-brand-teal truncate {{ $item['level'] === 1 ? 'font-bold text-gray-800' : ($item['level'] === 2 ? 'font-semibold text-gray-700 pl-4' : 'text-gray-500 pl-6') }}">
                        {{ $item['title'] }}
                    </a>
                @empty
                    <p class="text-gray-400 text-xs italic">Tidak ada sub-header detected.</p>
                @endforelse
            </nav>
        </div>

        {{-- Rendered Content Card --}}
        <div class="lg:col-span-3 bg-white rounded-2xl p-5 sm:p-8 lg:p-10 border border-gray-100 shadow-card overflow-hidden w-full">
            <div id="markdownRenderArea" class="markdown-body">
                {!! $htmlContent !!}
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
// Auto add IDs to headers for Table of Contents smooth scroll anchors
document.addEventListener("DOMContentLoaded", function() {
    const headings = document.querySelectorAll("#markdownRenderArea h1, #markdownRenderArea h2, #markdownRenderArea h3");
    headings.forEach(function(heading) {
        const textClean = heading.innerText.trim().replace(/[*_`#\\]/g, '');
        const slug = textClean.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        if (slug && !heading.id) {
            heading.id = slug;
        }
    });
});
</script>
@endpush
