@extends('layouts.app')

@section('title', 'Juklak & Juknis')
@section('page-title', 'Juklak & Juknis')

@section('nav-menu')
    <a href="{{ route('operator.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('operator.assignments.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-users-three text-xl"></i> Manajemen Guru & Juri
    </a>
    <a href="{{ route('operator.documents.index') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-book-open-text text-xl"></i> Juklak & Juknis
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Create Document --}}
    @if($program)
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <h3 class="font-bold text-gray-800 mb-4">Buat Dokumen Baru</h3>
        <form method="POST" action="{{ route('operator.documents.store') }}" class="space-y-4">
            @csrf
            <div class="flex gap-3">
                <select name="type" required class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal outline-none">
                    <option value="juklak">Juklak</option>
                    <option value="juknis">Juknis</option>
                </select>
                <input type="text" name="title" placeholder="Judul dokumen..." required
                    class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none">
            </div>
            <textarea name="content" rows="6" placeholder="Isi konten dokumen..." required
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none resize-y"></textarea>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl font-semibold text-sm transition-colors">
                <i class="ph ph-plus"></i> Simpan Draft
            </button>
        </form>
    </div>
    @endif

    {{-- Document List --}}
    <div class="space-y-4">
        @forelse($documents as $doc)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $doc->type === 'juklak' ? 'bg-blue-50' : 'bg-purple-50' }} flex items-center justify-center">
                            <i class="ph ph-book-open-text text-xl {{ $doc->type === 'juklak' ? 'text-blue-500' : 'text-purple-500' }}"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase {{ $doc->type === 'juklak' ? 'text-blue-600' : 'text-purple-600' }}">{{ $doc->type }}</span>
                                @if($doc->isPublished())
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">Published</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">Draft</span>
                                @endif
                            </div>
                            <h4 class="font-semibold text-gray-800 mt-0.5">{{ $doc->title }}</h4>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $doc->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if(!$doc->isPublished())
                        <form method="POST" action="{{ route('operator.documents.publish', $doc->id) }}">
                            @csrf
                            <button type="button" onclick="confirmPublish({{ $doc->id }}, '{{ $doc->title }}')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-xs font-medium transition-colors">
                                <i class="ph ph-paper-plane-tilt"></i> Publish
                            </button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('operator.documents.destroy', $doc->id) }}">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeleteDoc(this.closest('form'))"
                                class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <i class="ph ph-trash text-lg"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="mt-3 text-sm text-gray-600 line-clamp-3 bg-gray-50 rounded-xl p-3">
                    {{ Str::limit(strip_tags($doc->content), 200) }}
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-12 border border-gray-100 text-center text-gray-400">
            <i class="ph ph-book-open-text text-4xl mb-3 block opacity-40"></i>
            <p>Belum ada dokumen. Buat Juklak atau Juknis di atas.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmPublish(id, title) {
    Swal.fire({
        title: 'Publish Dokumen?',
        text: `"${title}" akan dipublikasikan dan dapat dilihat oleh semua pengguna.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Ya, Publish',
        cancelButtonText: 'Batal',
    }).then(r => {
        if (r.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/operator/documents/${id}/publish`;
            form.innerHTML = `<input type="hidden" name="_token" value="${window.CSRF_TOKEN}">`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
function confirmDeleteDoc(form) {
    Swal.fire({
        title: 'Hapus Dokumen?',
        text: 'Dokumen ini akan dihapus secara permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(r => { if (r.isConfirmed) form.submit(); });
}
</script>
@endpush
