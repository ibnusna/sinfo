@extends('layouts.app')

@section('title', 'Editor Markdown Panduan')
@section('page-title', 'Editor Markdown Panduan Projek')

@section('nav-menu')
    <a href="{{ route('operator.dashboard') }}" class="nav-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('operator.assignments.index') }}" class="nav-link {{ request()->routeIs('operator.assignments.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-users-three text-xl"></i> Manajemen Guru & Juri
    </a>
    <a href="{{ route('operator.markdown.index') }}" class="nav-link {{ request()->routeIs('operator.markdown.*') || request()->routeIs('operator.documents.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-book-open-text text-xl"></i> Panduan Projek (Markdown)
    </a>
    <a href="{{ route('operator.landing_photos.index') }}" class="nav-link {{ request()->routeIs('operator.landing_photos.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-image text-xl"></i> Foto Landing Page
    </a>
    <a href="{{ route('operator.score_settings.index') }}" class="nav-link {{ request()->routeIs('operator.score_settings.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-sliders text-xl"></i> Konfigurasi Penilaian
    </a>
    <a href="{{ route('panduan.show') }}" target="_blank" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2 border-t border-gray-100">
        <i class="ph ph-eye text-xl text-brand-teal"></i> Lihat Panduan Publik
    </a>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<style>
.preview-body h1 { font-size: 1.5rem; font-weight: 800; color: #0f766e; border-bottom: 2px solid #ccfbf1; margin-top: 1.25rem; margin-bottom: 0.75rem; }
.preview-body h2 { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-top: 1.25rem; margin-bottom: 0.5rem; }
.preview-body h3 { font-size: 1.1rem; font-weight: 600; color: #374151; margin-top: 1rem; margin-bottom: 0.4rem; }
.preview-body p { margin-bottom: 0.8rem; line-height: 1.6; color: #4b5563; }
.preview-body table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.85rem; }
.preview-body th { background: #0d9488; color: white; padding: 0.5rem 0.75rem; text-align: left; }
.preview-body td { padding: 0.5rem 0.75rem; border-bottom: 1px solid #e5e7eb; }
.preview-body blockquote { border-left: 4px solid #facc15; background: #fefce8; padding: 0.5rem 0.75rem; margin: 0.8rem 0; }
.preview-body code { background: #f1f5f9; color: #0f766e; padding: 0.1rem 0.3rem; border-radius: 0.25rem; font-size: 0.85em; }
</style>
@endpush

@section('content')
<div class="space-y-6 slide-up">

    {{-- Top Toolbar Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-card">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-teal-100 text-brand-teal flex items-center justify-center font-bold">
                <i class="ph ph-file-md text-2xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    Editor Markdown Panduan Projek
                    <span class="px-2.5 py-0.5 bg-brand-teal-light text-brand-teal text-xs font-bold rounded-full font-mono">{{ $cleanFilename }}</span>
                </h3>
                <p class="text-xs text-gray-500 mt-0.5">Edit konten dokumen Markdown di folder root/markdown projek secara terstruktur.</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            {{-- Document Selector Dropdown --}}
            <select onchange="location.href = '/operator/markdown/edit/' + this.value" class="border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-700 focus:border-brand-teal outline-none bg-gray-50 font-medium">
                @foreach($availableFiles as $fname => $label)
                    <option value="{{ $fname }}" {{ $cleanFilename === $fname ? 'selected' : '' }}>{{ $fname }} ({{ $label }})</option>
                @endforeach
            </select>

            <button onclick="openNewDocModal()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                + Baru
            </button>

            <a href="{{ route('panduan.show', $cleanFilename) }}" target="_blank" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition-colors flex items-center gap-1">
                <i class="ph ph-arrow-square-out text-sm"></i> View Publik
            </a>
        </div>
    </div>

    {{-- Main Editor Form --}}
    <form method="POST" action="{{ route('operator.markdown.save') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="filename" value="{{ $cleanFilename }}">

        <div class="grid lg:grid-cols-2 gap-6 items-stretch">

            {{-- Left Column: Raw Textarea Editor --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card flex flex-col overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/70 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="ph ph-code text-brand-teal"></i> Markdown Editor Source
                    </span>
                    <span class="text-[11px] text-gray-400 font-mono" id="charCount">0 Karakter</span>
                </div>
                <div class="p-4 flex-1">
                    <textarea name="content" id="markdownInput" rows="22" required
                        class="w-full h-full min-h-[500px] font-mono text-xs sm:text-sm text-gray-800 leading-relaxed p-4 border border-gray-200 rounded-xl focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none resize-y bg-slate-50 focus:bg-white selection:bg-brand-teal selection:text-white"
                        placeholder="Tulis sintaks markdown di sini...">{{ $rawContent }}</textarea>
                </div>
            </div>

            {{-- Right Column: Live Rendered Preview --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-card flex flex-col overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/70 flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="ph ph-eye text-brand-teal"></i> Live Preview Hasil Render
                    </span>
                    <span class="text-[11px] text-brand-teal font-semibold">Real-time</span>
                </div>
                <div class="p-6 flex-1 overflow-y-auto max-h-[550px]">
                    <div id="livePreview" class="preview-body"></div>
                </div>
            </div>

        </div>

        {{-- Bottom Save Action --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-card flex justify-between items-center">
            <span class="text-xs text-gray-500">
                <i class="ph ph-info text-brand-teal"></i> Perubahan disimpan langsung ke file <code class="font-mono text-brand-teal bg-teal-50 px-1 py-0.5 rounded">{{ $cleanFilename }}</code> di root projek.
            </span>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl font-bold text-sm transition-all shadow-md hover:shadow-soft cursor-pointer">
                <i class="ph ph-floppy-disk text-lg"></i> Simpan Perubahan Markdown
            </button>
        </div>
    </form>

</div>

{{-- MODAL BUAT DOKUMEN MARKDOWN BARU --}}
<div id="newDocModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-gray-100 transform scale-95 transition-transform duration-200">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="ph ph-file-plus text-brand-teal text-xl"></i>
                Buat Dokumen Markdown Baru
            </h3>
            <button onclick="closeNewDocModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('operator.markdown.create') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Judul Dokumen</label>
                <input type="text" name="title" required placeholder="Contoh: Panduan Penilaian IPA"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal outline-none">
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeNewDocModal()" class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                    Buat Berkas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const input = document.getElementById('markdownInput');
const preview = document.getElementById('livePreview');
const charCount = document.getElementById('charCount');

function updatePreview() {
    const val = input.value;
    charCount.innerText = val.length + ' Karakter';
    if (typeof marked !== 'undefined') {
        preview.innerHTML = marked.parse(val);
    } else {
        preview.innerText = val;
    }
}

input.addEventListener('input', updatePreview);
document.addEventListener('DOMContentLoaded', updatePreview);

function openNewDocModal() {
    const modal = document.getElementById('newDocModal');
    modal.classList.remove('hidden');
    setTimeout(() => modal.classList.remove('opacity-0'), 10);
}
function closeNewDocModal() {
    const modal = document.getElementById('newDocModal');
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 200);
}
</script>
@endpush
