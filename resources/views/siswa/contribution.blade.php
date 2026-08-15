@extends('layouts.app')

@section('title', 'Data Kontribusi')
@section('page-title', 'Data Kontribusi')

@section('nav-menu')
    <a href="{{ route('siswa.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors text-gray-600 hover:bg-gray-50">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('siswa.product_metadata.index') ?? '#' }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors text-gray-600 hover:bg-gray-50">
        <i class="ph ph-info text-xl"></i> Metadata Produk
    </a>
    <a href="{{ route('siswa.contribution.index') ?? '#' }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-users-three text-xl"></i> Kontribusi
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3 text-amber-800">
        <i class="ph ph-warning-circle text-xl shrink-0 mt-0.5"></i>
        <div>
            <p class="text-sm font-medium">Data kontribusi bersifat PRIVAT dan hanya dapat dilihat oleh Guru.</p>
        </div>
    </div>

    @if(!$isLeader)
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card text-center text-gray-500">
            <i class="ph ph-lock-key text-3xl mb-2 block text-gray-400"></i>
            <p>Hanya ketua kelompok yang dapat mengisi data kontribusi.</p>
        </div>
    @endif

    @if($alreadySubmitted)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
            <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-800">Data Kontribusi Anggota</h3>
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200">
                    <i class="ph ph-check-circle mr-1"></i> Sudah Disubmit
                </span>
            </div>
            <div class="p-5 space-y-3">
                @foreach($members as $member)
                    @php
                        $contrib = isset($contributions) ? $contributions->where('student_user_id', $member->student_user_id)->first() : null;
                        $isContrib = $contrib ? $contrib->is_contributed : true;
                        $reason = $contrib ? $contrib->reason : null;
                    @endphp
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white border {{ $isContrib ? 'border-gray-100' : 'border-red-200 bg-red-50' }} rounded-xl">
                        <div class="flex items-center gap-3 mb-2 sm:mb-0">
                            <div class="w-10 h-10 rounded-full {{ $isContrib ? 'bg-indigo-50 text-indigo-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center font-bold">
                                {{ strtoupper(substr($member->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $member->name }}</p>
                            </div>
                        </div>
                        <div class="sm:text-right">
                            @if($isContrib)
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-green-600">
                                    <i class="ph ph-check-circle text-lg"></i> Berkontribusi Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-red-600 mb-1">
                                    <i class="ph ph-x-circle text-lg"></i> Tidak Berkontribusi
                                </span>
                                <p class="text-xs text-red-500 italic max-w-xs">"{{ $reason }}"</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif($isLeader)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Form Evaluasi Kontribusi</h3>
            </div>
            <div class="p-5">
                <form action="{{ route('siswa.contribution.store') }}" method="POST" id="contributionForm">
                    @csrf
                    
                    <div class="mb-6 space-y-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Mode Evaluasi:</label>
                        
                        <div class="border border-gray-200 rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition-colors" onclick="document.getElementById('modeA').click()">
                            <div class="flex items-start gap-3">
                                <input type="radio" id="modeA" name="mode" value="all_active" class="mt-1 text-brand-teal focus:ring-brand-teal" checked onchange="toggleMode()">
                                <div>
                                    <h4 class="font-semibold text-gray-800">Semua anggota berkontribusi (Mode A)</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Pilih ini jika seluruh anggota kelompok bekerja dengan baik dan aktif.</p>
                                </div>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition-colors" onclick="document.getElementById('modeB').click()">
                            <div class="flex items-start gap-3">
                                <input type="radio" id="modeB" name="mode" value="some_inactive" class="mt-1 text-brand-teal focus:ring-brand-teal" onchange="toggleMode()">
                                <div>
                                    <h4 class="font-semibold text-gray-800">Ada anggota tidak aktif (Mode B)</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Pilih ini jika ada satu atau lebih anggota yang sama sekali tidak berkontribusi.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="membersList" class="hidden space-y-4 mb-6 pt-4 border-t border-gray-100">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Tandai anggota yang TIDAK berkontribusi:</h4>
                        
                        @foreach($members as $member)
                            <div class="p-4 border border-gray-200 rounded-xl bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center font-bold text-gray-600 text-xs">
                                            {{ strtoupper(substr($member->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $member->name }}</span>
                                    </div>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="not_contributing[]" value="{{ $member->student_user_id }}" class="text-red-500 focus:ring-red-500 rounded" onchange="toggleReason('{{ $member->student_user_id }}', this.checked)">
                                        <span class="text-sm text-red-600 font-medium">Tidak Berkontribusi</span>
                                    </label>
                                </div>
                                <div id="reason_container_{{ $member->student_user_id }}" class="hidden pl-11">
                                    <input type="text" name="reasons[{{ $member->student_user_id }}]" id="reason_{{ $member->student_user_id }}" placeholder="Alasan mengapa tidak berkontribusi... (Wajib diisi)" class="w-full text-sm rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 bg-white">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl font-semibold text-sm transition-colors shadow-soft" onclick="return confirm('Apakah Anda yakin dengan data kontribusi ini? Data tidak dapat diubah setelah disubmit.')">
                            <i class="ph ph-paper-plane-tilt mr-2"></i> Submit Data Kontribusi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function toggleMode() {
    const isModeB = document.getElementById('modeB').checked;
    const membersList = document.getElementById('membersList');
    
    if (isModeB) {
        membersList.classList.remove('hidden');
    } else {
        membersList.classList.add('hidden');
        // Reset all checkboxes and reason fields if Mode A is selected
        document.querySelectorAll('input[name="not_contributing[]"]').forEach(cb => {
            cb.checked = false;
            toggleReason(cb.value, false);
        });
    }
}

function toggleReason(userId, isChecked) {
    const container = document.getElementById('reason_container_' + userId);
    const input = document.getElementById('reason_' + userId);
    
    if (isChecked) {
        container.classList.remove('hidden');
        input.required = true;
    } else {
        container.classList.add('hidden');
        input.required = false;
        input.value = '';
    }
}

document.getElementById('contributionForm')?.addEventListener('submit', function(e) {
    const isModeB = document.getElementById('modeB').checked;
    if (isModeB) {
        const checkedBoxes = document.querySelectorAll('input[name="not_contributing[]"]:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Jika memilih Mode B (Ada anggota tidak aktif), Anda harus menandai setidaknya satu anggota yang tidak berkontribusi. Jika tidak ada, silakan pilih Mode A.');
        }
    }
});
</script>
@endpush
