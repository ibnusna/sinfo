@extends('layouts.app')

@section('title', 'Manajemen Kelompok')
@section('page-title', 'Manajemen Kelompok')

@section('nav-menu')
    <a href="{{ route('guru.dashboard') }}" class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('guru.groups.index') }}" class="nav-link {{ request()->routeIs('guru.groups.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-users text-xl"></i> Manajemen Kelompok
    </a>
    <a href="{{ route('guru.assessments.index') }}" class="nav-link {{ request()->routeIs('guru.assessments.*') || request()->routeIs('guru.scores.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-clipboard-text text-xl"></i> Penilaian IPA
    </a>
    <a href="{{ route('panduan.show') }}" target="_blank" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2 border-t border-gray-100">
        <i class="ph ph-book-bookmark text-xl text-brand-teal"></i> Panduan Projek IPA
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Create Group Button --}}
    @if($program && $systemStatus === 'open')
    <div class="flex justify-end">
        <button onclick="openModal('modal-create-group')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
            <i class="ph ph-plus"></i> Buat Kelompok Baru
        </button>
    </div>
    @elseif($systemStatus === 'closed')
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-orange-700 text-sm">
        <i class="ph ph-lock-key mr-2"></i> Sistem sedang ditutup. Anda tidak dapat membuat kelompok baru.
    </div>
    @endif

    {{-- Group List --}}
    @forelse($groups as $group)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="p-5">
            <div class="flex items-start justify-between">
                <div>
                    <h4 class="font-bold text-gray-800">{{ $group->name }}</h4>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Ketua: <span class="font-medium text-gray-700">{{ $group->leader_data?->getDisplayName() ?? 'Belum ditentukan' }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ $group->member_count }} Anggota</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($group->poster)
                        <span class="px-2 py-1 text-xs rounded-full font-medium
                            {{ $group->poster->status === 'approved' ? 'bg-green-100 text-green-700' :
                               ($group->poster->status === 'submitted' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                            <i class="ph ph-image mr-1"></i>{{ ucfirst($group->poster->status) }}
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full font-medium bg-gray-100 text-gray-500">Belum ada poster</span>
                    @endif
                    <form method="POST" action="{{ route('guru.groups.destroy', $group->id) }}" id="form-del-{{ $group->id }}">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDelete({{ $group->id }})"
                            class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="ph ph-trash text-lg"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl p-12 border border-gray-100 text-center text-gray-400 shadow-card">
        <i class="ph ph-users text-4xl mb-3 block opacity-40"></i>
        <p class="font-medium">Belum ada kelompok.</p>
        <p class="text-sm mt-1">Klik "Buat Kelompok Baru" untuk memulai.</p>
    </div>
    @endforelse

</div>

{{-- Modal Create Group --}}
<div id="modal-create-group" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 bg-gray-900/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg slide-up">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800">Buat Kelompok Baru</h3>
            <button onclick="closeModal('modal-create-group')" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('guru.groups.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelompok</label>
                <input type="text" name="name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none" placeholder="Contoh: Kelompok Matahari">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ketua Kelompok</label>
                <select name="leader_user_id" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal outline-none">
                    <option value="">-- Pilih Ketua --</option>
                    @foreach($allStudents as $student)
                        <option value="{{ $student->user_id }}">{{ $student->nama }} ({{ $student->nis }}) — {{ $student->kelas->nama_kelas ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Anggota Kelompok (bisa pilih banyak)</label>
                <select name="member_ids[]" multiple required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal outline-none" size="6">
                    @foreach($allStudents as $student)
                        <option value="{{ $student->user_id }}">{{ $student->nama }} ({{ $student->nis }}) — {{ $student->kelas->nama_kelas ?? '' }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Tahan Ctrl/Cmd untuk memilih lebih dari satu. Ketua akan otomatis ditambahkan sebagai anggota.</p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-create-group')" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-xl text-sm font-medium">Batal</button>
                <button type="submit" class="px-5 py-2 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl text-sm font-semibold transition-colors">
                    <i class="ph ph-floppy-disk mr-1"></i> Simpan Kelompok
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Kelompok?',
        text: 'Seluruh data kelompok termasuk anggota akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then(r => { if (r.isConfirmed) document.getElementById(`form-del-${id}`).submit(); });
}
</script>
@endpush
