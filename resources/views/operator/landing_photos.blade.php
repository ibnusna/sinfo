@extends('layouts.app')

@section('title', 'Manajemen Foto Landing Page')
@section('page-title', 'Kelola Foto Landing Page')

@section('nav-menu')
    <a href="{{ route('operator.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('operator.assignments.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-users-three text-xl"></i> Manajemen Guru & Juri
    </a>
    <a href="{{ route('operator.documents.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-book-open-text text-xl"></i> Juklak & Juknis
    </a>
    <a href="{{ route('operator.landing_photos.index') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-image text-xl"></i> Foto Landing Page
    </a>
    <a href="{{ route('operator.markdown.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-file-md text-xl"></i> Markdown Panduan
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-card">
        <div>
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="ph ph-image-square text-brand-teal text-2xl"></i>
                Kelola Pengelolaan Foto Landing Page
            </h3>
            <p class="text-xs text-gray-500 mt-1">Tambah, perbarui, atau hapus foto yang tampil secara dinamis pada landing page publik.</p>
        </div>
        <button onclick="openUploadModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl font-semibold text-sm transition-all shadow-sm hover:shadow-soft shrink-0 cursor-pointer">
            <i class="ph ph-plus-circle text-lg"></i> Unggah Foto Baru
        </button>
    </div>

    {{-- Section Grid --}}
    <div class="space-y-8">

        {{-- 1. HERO SECTION PHOTO --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full uppercase">Section 1</span>
                    <h4 class="font-bold text-gray-800 text-base">Hero Section (Foto Utama Top)</h4>
                </div>
                <span class="text-xs text-gray-400 font-medium">{{ $heroPhotos->count() }} Foto</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($heroPhotos as $photo)
                    @include('operator.partials.photo_card', ['photo' => $photo])
                @empty
                    <div class="col-span-full py-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <i class="ph ph-image text-3xl mb-2 block opacity-40"></i>
                        <p class="text-sm">Belum ada foto khusus Hero. Landing page menggunakan foto default.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 2. ABOUT SECTION PHOTO --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-teal-100 text-teal-800 text-xs font-bold rounded-full uppercase">Section 2</span>
                    <h4 class="font-bold text-gray-800 text-base">About Section (Tentang Program)</h4>
                </div>
                <span class="text-xs text-gray-400 font-medium">{{ $aboutPhotos->count() }} Foto</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($aboutPhotos as $photo)
                    @include('operator.partials.photo_card', ['photo' => $photo])
                @empty
                    <div class="col-span-full py-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <i class="ph ph-image text-3xl mb-2 block opacity-40"></i>
                        <p class="text-sm">Belum ada foto khusus About Section.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- 3. GALLERY SECTION PHOTOS --}}
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-bold rounded-full uppercase">Section 3</span>
                    <h4 class="font-bold text-gray-800 text-base">Gallery Showcase (Galeri Kegiatan)</h4>
                </div>
                <span class="text-xs text-gray-400 font-medium">{{ $galleryPhotos->count() }} Foto</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($galleryPhotos as $photo)
                    @include('operator.partials.photo_card', ['photo' => $photo])
                @empty
                    <div class="col-span-full py-8 text-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                        <i class="ph ph-images text-3xl mb-2 block opacity-40"></i>
                        <p class="text-sm">Belum ada foto di Galeri.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- MODAL UPLOAD FOTO BARU --}}
<div id="uploadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-100 transform scale-95 transition-transform duration-200" id="uploadModalBox">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="ph ph-upload-simple text-brand-teal text-xl"></i>
                Unggah Foto Landing Page
            </h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('operator.landing_photos.store') }}" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Judul Foto</label>
                <input type="text" name="title" required placeholder="Contoh: Exhibition Day Science Food Festival"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Posisi / Section</label>
                    <select name="section" required id="uploadSection" onchange="toggleCategorySelect(this.value, 'uploadCategoryGroup')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-brand-teal outline-none bg-white">
                        <option value="gallery" selected>Gallery Showcase</option>
                        <option value="hero">Hero Section</option>
                        <option value="about">About Section</option>
                    </select>
                </div>
                <div id="uploadCategoryGroup">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Kategori Galeri</label>
                    <select name="category" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-brand-teal outline-none bg-white">
                        <option value="exhibition">Exhibition</option>
                        <option value="preparation">Preparation</option>
                        <option value="presentation">Presentation</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="1" min="1"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Pilih Berkas Gambar</label>
                <input type="file" name="image" required accept="image/*" onchange="previewUploadImage(this)"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-teal-light file:text-brand-teal hover:file:bg-teal-100 cursor-pointer">
                <span class="text-[11px] text-gray-400 mt-1 block">Format: JPG, PNG, WEBP, GIF. Maksimal 5 MB.</span>
            </div>

            <div id="uploadPreviewBox" class="hidden mt-2 rounded-xl overflow-hidden border border-gray-200 bg-gray-50 max-h-48 flex justify-center items-center">
                <img id="uploadPreviewImg" src="" class="object-cover max-h-48 w-full">
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeUploadModal()" class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                    Simpan Foto
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT FOTO --}}
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-200 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-100 transform scale-95 transition-transform duration-200">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <i class="ph ph-note-pencil text-brand-teal text-xl"></i>
                Edit Informasi Foto
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-lg hover:bg-gray-100">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Judul Foto</label>
                <input type="text" name="title" id="editTitle" required
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Posisi / Section</label>
                    <select name="section" id="editSection" required onchange="toggleCategorySelect(this.value, 'editCategoryGroup')"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-brand-teal outline-none bg-white">
                        <option value="gallery">Gallery Showcase</option>
                        <option value="hero">Hero Section</option>
                        <option value="about">About Section</option>
                    </select>
                </div>
                <div id="editCategoryGroup">
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Kategori Galeri</label>
                    <select name="category" id="editCategory" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-brand-teal outline-none bg-white">
                        <option value="exhibition">Exhibition</option>
                        <option value="preparation">Preparation</option>
                        <option value="presentation">Presentation</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="editSortOrder" min="1"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal outline-none">
                </div>
                <div class="flex items-center pt-5">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="editIsActive" value="1" class="w-4 h-4 rounded text-brand-teal focus:ring-brand-teal">
                        <span class="text-sm font-medium text-gray-700">Aktif Tampil</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Ganti Berkas Gambar (Opsional)</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer">
                <span class="text-[11px] text-gray-400 mt-1 block">Biarkan kosong jika tidak ingin mengganti file gambar saat ini.</span>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl text-sm font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleCategorySelect(section, groupElemId) {
    const group = document.getElementById(groupElemId);
    if (section === 'gallery') {
        group.classList.remove('hidden');
    } else {
        group.classList.add('hidden');
    }
}

function openUploadModal() {
    const modal = document.getElementById('uploadModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        document.getElementById('uploadModalBox').classList.remove('scale-95');
    }, 10);
}

function closeUploadModal() {
    const modal = document.getElementById('uploadModal');
    modal.classList.add('opacity-0');
    document.getElementById('uploadModalBox').classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

function previewUploadImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('uploadPreviewImg').src = e.target.result;
            document.getElementById('uploadPreviewBox').classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function openEditModal(photo) {
    const form = document.getElementById('editForm');
    form.action = `/operator/landing-photos/${photo.id}`;

    document.getElementById('editTitle').value = photo.title;
    document.getElementById('editSection').value = photo.section;
    document.getElementById('editCategory').value = photo.category || 'exhibition';
    document.getElementById('editSortOrder').value = photo.sort_order;
    document.getElementById('editIsActive').checked = photo.is_active;

    toggleCategorySelect(photo.section, 'editCategoryGroup');

    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
    }, 10);
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.add('opacity-0');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

function confirmDeletePhoto(form) {
    Swal.fire({
        title: 'Hapus Foto?',
        text: 'Foto ini akan dihapus dari sistem dan tidak tampil lagi di landing page.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(r => {
        if (r.isConfirmed) form.submit();
    });
}
</script>
@endpush
