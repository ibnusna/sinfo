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
                        <a href="{{ route('guru.assessments.index') }}" class="px-2.5 py-1 text-xs rounded-full font-bold flex items-center gap-1 transition-all hover:scale-105
                            {{ $group->poster->status === 'approved' ? 'bg-green-100 text-green-700 hover:bg-green-200' :
                               ($group->poster->status === 'revision' ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-blue-100 text-blue-700 hover:bg-blue-200') }}"
                           title="Klik untuk menilai/verifikasi poster">
                            <i class="ph ph-image text-sm"></i>
                            Poster: {{ $group->poster->status === 'approved' ? 'Disetujui' : ($group->poster->status === 'revision' ? 'Perlu Revisi' : 'Menunggu Review') }}
                        </a>
                    @else
                        <span class="px-2.5 py-1 text-xs rounded-full font-medium bg-gray-100 text-gray-500 flex items-center gap-1">
                            <i class="ph ph-image text-sm opacity-50"></i> Belum ada poster
                        </span>
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
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg slide-up overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-brand-teal-light text-brand-teal flex items-center justify-center font-bold">
                    <i class="ph ph-users-three text-xl"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-800">Buat Kelompok Baru</h3>
                    <p class="text-xs text-gray-400">Pilih ketua dan anggota kelompok siswa</p>
                </div>
            </div>
            <button onclick="closeModal('modal-create-group')" class="text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-gray-100">
                <i class="ph ph-x text-xl"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('guru.groups.store') }}" class="p-6 space-y-4" onsubmit="return validateGroupForm(event)">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kelompok</label>
                <input type="text" name="name" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none transition-all" placeholder="Contoh: Kelompok Matahari">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Ketua Kelompok</label>
                {{-- Leader Autocomplete Search & Class Filter --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div class="relative sm:col-span-2" id="leader-autocomplete-container">
                        <div class="relative">
                            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="leader-search-input" oninput="onLeaderSearchInput(this.value)"
                                   onfocus="onLeaderSearchInput(this.value)"
                                   placeholder="Ketik nama atau NIS ketua..."
                                   class="w-full border border-gray-200 rounded-xl pl-9 pr-10 py-2.5 text-sm focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none transition-all"
                                   autocomplete="off">
                            <button type="button" id="clear-leader-btn" onclick="clearLeaderSelection()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors p-1">
                                <i class="ph ph-x-circle text-lg"></i>
                            </button>
                        </div>
                        <input type="hidden" name="leader_user_id" id="leader-user-id" required>
                        
                        {{-- Autocomplete Results list --}}
                        <div id="leader-search-results" class="hidden absolute left-0 right-0 z-50 mt-1 max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-lg divide-y divide-gray-100 scrollbar-thin">
                        </div>
                    </div>
                    <div>
                        <select id="leader-class-filter" onchange="onLeaderSearchInput(document.getElementById('leader-search-input').value)"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-brand-teal outline-none transition-all text-gray-600 font-medium bg-white">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-gray-700">Anggota Kelompok</label>
                    <span id="selected-member-count" class="text-xs font-semibold px-2 py-0.5 bg-brand-teal-light text-brand-teal rounded-full">
                        0 Anggota Dipilih
                    </span>
                </div>

                {{-- Member Autocomplete Search & Class Filter --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mb-3">
                    <div class="relative sm:col-span-2" id="member-autocomplete-container">
                        <div class="relative">
                            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="member-search-input" oninput="onMemberSearchInput(this.value)"
                                   onfocus="onMemberSearchInput(this.value)"
                                   placeholder="Ketik nama atau NIS untuk menambah anggota..."
                                   class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:border-brand-teal focus:ring-2 focus:ring-brand-teal-light outline-none transition-all"
                                   autocomplete="off">
                        </div>
                        
                        {{-- Autocomplete Results --}}
                        <div id="member-search-results" class="hidden absolute left-0 right-0 z-50 mt-1 max-h-56 overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-lg divide-y divide-gray-100 scrollbar-thin">
                        </div>
                    </div>
                    <div>
                        <select id="member-class-filter" onchange="onMemberSearchInput(document.getElementById('member-search-input').value)"
                                class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-brand-teal outline-none transition-all text-gray-600 font-medium bg-white">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Selected Members Tags/Chips Container --}}
                <div id="selected-members-container" class="flex flex-wrap gap-2 p-3.5 border border-gray-200 rounded-xl min-h-24 bg-gray-50/50">
                    <p id="no-members-placeholder" class="text-xs text-gray-400 italic m-auto">Belum ada anggota yang ditambahkan.</p>
                </div>
                
                {{-- Hidden Inputs for Form Submit --}}
                <div id="member-hidden-inputs"></div>

                <p id="member-error-msg" class="hidden text-xs text-red-500 mt-1.5 font-medium flex items-center gap-1">
                    <i class="ph ph-warning-circle"></i> Minimal satu anggota (selain ketua) wajib dipilih.
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('modal-create-group')" class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-xl text-sm font-medium transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl text-sm font-semibold transition-all shadow-sm flex items-center gap-1.5">
                    <i class="ph ph-floppy-disk text-base"></i> Simpan Kelompok
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Expose all students list to JS for dynamic filtering
const allStudentsList = @json($allStudentsData);
const selectedMemberIds = new Set();
let previousLeaderId = '';

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    // Reset filters and selection on open
    clearLeaderSelection();
    
    const memberSearch = document.getElementById('member-search-input');
    const memberClass = document.getElementById('member-class-filter');
    const leaderClass = document.getElementById('leader-class-filter');
    
    if (memberSearch) memberSearch.value = '';
    if (memberClass) memberClass.value = '';
    if (leaderClass) leaderClass.value = '';
    
    selectedMemberIds.clear();
    renderSelectedMembers();
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function renderSelectedMembers() {
    const container = document.getElementById('selected-members-container');
    const hiddenInputs = document.getElementById('member-hidden-inputs');
    const countEl = document.getElementById('selected-member-count');
    const errorEl = document.getElementById('member-error-msg');
    
    container.innerHTML = '';
    hiddenInputs.innerHTML = '';
    
    const leaderId = document.getElementById('leader-user-id').value;
    
    if (selectedMemberIds.size === 0) {
        container.innerHTML = '<p id="no-members-placeholder" class="text-xs text-gray-400 italic m-auto">Belum ada anggota yang ditambahkan.</p>';
    } else {
        selectedMemberIds.forEach(userId => {
            const student = allStudentsList.find(s => s.user_id == userId);
            if (student) {
                const isLeader = (userId == leaderId);
                const chip = document.createElement('div');
                chip.className = 'flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-xl shadow-xs text-xs';
                
                let deleteButton = '';
                let badge = '';
                
                if (isLeader) {
                    badge = '<span class="px-1.5 py-0.2 text-[9px] font-bold bg-amber-100 text-amber-700 rounded shrink-0">KETUA</span>';
                    deleteButton = `<span class="text-gray-300 cursor-not-allowed" title="Ketua kelompok tidak bisa dihapus dari anggota"><i class="ph ph-lock text-base"></i></span>`;
                } else {
                    deleteButton = `<button type="button" onclick="removeMember(${userId})" class="text-gray-400 hover:text-red-500 p-0.5 rounded-lg transition-colors"><i class="ph ph-x-circle text-base"></i></button>`;
                }
                
                chip.innerHTML = `
                    <span class="font-semibold text-gray-800">${student.nama}</span>
                    <span class="text-[10px] text-gray-400 font-medium">(${student.kelas})</span>
                    ${badge}
                    ${deleteButton}
                `;
                container.appendChild(chip);
                
                // Add hidden input for form submission
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'member_ids[]';
                hiddenInput.value = userId;
                hiddenInputs.appendChild(hiddenInput);
            }
        });
    }
    
    if (countEl) {
        countEl.textContent = `${selectedMemberIds.size} Anggota Dipilih`;
    }
    if (errorEl && selectedMemberIds.size > 1) {
        errorEl.classList.add('hidden');
    }
}

// Custom Search-as-you-type Autocomplete for Leader Selection
function onLeaderSearchInput(query) {
    const resultsContainer = document.getElementById('leader-search-results');
    const classFilter = document.getElementById('leader-class-filter').value;
    const q = (query || '').toLowerCase().trim();

    resultsContainer.innerHTML = '';
    
    let matchCount = 0;
    allStudentsList.forEach(student => {
        const matchesQuery = !q || student.nama.toLowerCase().includes(q) || student.nis.includes(q);
        const matchesClass = !classFilter || student.kelas === classFilter;

        if (matchesQuery && matchesClass) {
            matchCount++;
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'w-full flex items-center justify-between px-4 py-2.5 hover:bg-brand-teal-light/40 transition-colors text-left text-xs border-b border-gray-50 last:border-b-0 focus:outline-none';
            item.onclick = function() {
                selectLeader(student.user_id, student.nama, student.nis, student.kelas);
            };
            item.innerHTML = `
                <div class="min-w-0 mr-2">
                    <span class="font-bold text-gray-800 block truncate">${student.nama}</span>
                    <span class="text-gray-400 text-[10px]">NIS: ${student.nis}</span>
                </div>
                <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-gray-100 text-gray-600 shrink-0">${student.kelas}</span>
            `;
            resultsContainer.appendChild(item);
        }
    });

    if (matchCount > 0) {
        resultsContainer.classList.remove('hidden');
    } else {
        resultsContainer.innerHTML = '<div class="p-3 text-xs text-gray-400 text-center">Siswa tidak ditemukan</div>';
        resultsContainer.classList.remove('hidden');
    }
}

function selectLeader(userId, nama, nis, kelas) {
    const searchInput = document.getElementById('leader-search-input');
    const userIdInput = document.getElementById('leader-user-id');
    const clearBtn = document.getElementById('clear-leader-btn');
    const resultsContainer = document.getElementById('leader-search-results');

    userIdInput.value = userId;
    searchInput.value = `${nama} (${nis}) — ${kelas}`;
    searchInput.readOnly = true;
    searchInput.classList.add('bg-gray-50', 'text-brand-teal', 'font-semibold');
    
    clearBtn.classList.remove('hidden');
    resultsContainer.classList.add('hidden');

    onLeaderSelect(userId);
}

function clearLeaderSelection() {
    const searchInput = document.getElementById('leader-search-input');
    const userIdInput = document.getElementById('leader-user-id');
    const clearBtn = document.getElementById('clear-leader-btn');
    const resultsContainer = document.getElementById('leader-search-results');

    userIdInput.value = '';
    searchInput.value = '';
    searchInput.readOnly = false;
    searchInput.classList.remove('bg-gray-50', 'text-brand-teal', 'font-semibold');
    
    clearBtn.classList.add('hidden');
    resultsContainer.classList.add('hidden');

    onLeaderSelect('');
}

function onLeaderSelect(leaderUserId) {
    if (previousLeaderId) {
        selectedMemberIds.delete(previousLeaderId);
    }
    
    if (leaderUserId) {
        selectedMemberIds.add(leaderUserId);
        previousLeaderId = leaderUserId;
    } else {
        previousLeaderId = '';
    }
    
    renderSelectedMembers();
}

// Custom Search-as-you-type Autocomplete for Member Selection
function onMemberSearchInput(query) {
    const resultsContainer = document.getElementById('member-search-results');
    const classFilter = document.getElementById('member-class-filter').value;
    const q = (query || '').toLowerCase().trim();

    resultsContainer.innerHTML = '';
    
    let matchCount = 0;
    allStudentsList.forEach(student => {
        const matchesQuery = !q || student.nama.toLowerCase().includes(q) || student.nis.includes(q);
        const matchesClass = !classFilter || student.kelas === classFilter;
        const isAlreadySelected = selectedMemberIds.has(student.user_id);

        if (matchesQuery && matchesClass && !isAlreadySelected) {
            matchCount++;
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'w-full flex items-center justify-between px-4 py-2.5 hover:bg-brand-teal-light/40 transition-colors text-left text-xs border-b border-gray-50 last:border-b-0 focus:outline-none';
            item.onclick = function() {
                addMember(student.user_id);
            };
            item.innerHTML = `
                <div class="min-w-0 mr-2">
                    <span class="font-bold text-gray-800 block truncate">${student.nama}</span>
                    <span class="text-gray-400 text-[10px]">NIS: ${student.nis}</span>
                </div>
                <span class="px-2 py-0.5 text-[9px] font-bold rounded bg-gray-100 text-gray-600 shrink-0">${student.kelas}</span>
            `;
            resultsContainer.appendChild(item);
        }
    });

    if (matchCount > 0) {
        resultsContainer.classList.remove('hidden');
    } else {
        resultsContainer.innerHTML = '<div class="p-3 text-xs text-gray-400 text-center">Siswa tidak ditemukan atau sudah dipilih</div>';
        resultsContainer.classList.remove('hidden');
    }
}

function addMember(userId) {
    selectedMemberIds.add(userId);
    renderSelectedMembers();
    
    const searchInput = document.getElementById('member-search-input');
    searchInput.value = '';
    searchInput.focus();
    
    document.getElementById('member-search-results').classList.add('hidden');
}

function removeMember(userId) {
    selectedMemberIds.delete(userId);
    renderSelectedMembers();
}

// Close results dropdown when clicking outside
document.addEventListener('click', function(e) {
    const leaderContainer = document.getElementById('leader-autocomplete-container');
    const leaderResults = document.getElementById('leader-search-results');
    if (leaderContainer && leaderResults && !leaderContainer.contains(e.target)) {
        leaderResults.classList.add('hidden');
    }
    
    const memberContainer = document.getElementById('member-autocomplete-container');
    const memberResults = document.getElementById('member-search-results');
    if (memberContainer && memberResults && !memberContainer.contains(e.target)) {
        memberResults.classList.add('hidden');
    }
});

function validateGroupForm(e) {
    const errorEl = document.getElementById('member-error-msg');
    const leaderInput = document.getElementById('leader-user-id');
    
    if (!leaderInput.value) {
        e.preventDefault();
        Swal.fire({
            title: 'Ketua Belum Dipilih',
            text: 'Harap pilih ketua kelompok terlebih dahulu.',
            icon: 'warning',
            confirmButtonColor: '#0d9488'
        });
        return false;
    }

    if (selectedMemberIds.size < 2) {
        e.preventDefault();
        if (errorEl) errorEl.classList.remove('hidden');
        Swal.fire({
            title: 'Anggota Kelompok Kurang',
            text: 'Kelompok harus memiliki minimal 1 anggota selain ketua kelompok.',
            icon: 'warning',
            confirmButtonColor: '#0d9488'
        });
        return false;
    }
    return true;
}

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
