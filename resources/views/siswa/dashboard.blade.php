@extends('layouts.app')

@section('title', 'Dashboard Siswa')
@section('page-title', 'Dashboard Siswa')

@section('nav-menu')
    <a href="{{ route('siswa.dashboard') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('siswa.product_metadata.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-cooking-pot text-xl"></i> Metadata Produk
    </a>
    <a href="{{ route('siswa.contribution.index') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-users-three text-xl"></i> Kontribusi Anggota
    </a>
    <a href="{{ route('panduan.show') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors mt-2">
        <i class="ph ph-book-bookmark text-xl"></i> Panduan Projek IPA
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Greeting --}}
    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl p-6 text-white shadow-soft">
        <p class="text-indigo-100 text-sm">Selamat datang,</p>
        <h3 class="text-xl font-bold mt-1">{{ $user->getDisplayName() }}</h3>
        @if($isLeader)
            <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 bg-white/20 rounded-full text-xs font-bold">
                <i class="ph ph-crown"></i> Ketua Kelompok
            </span>
        @endif
    </div>

    {{-- My Group --}}
    @if($myGroup)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Kelompok Saya</h3>
        </div>
        <div class="p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                    <i class="ph ph-users text-2xl text-indigo-500"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">{{ $myGroup->name }}</h4>
                    <p class="text-sm text-gray-500">Ketua: {{ $myGroup->getLeader()?->getDisplayName() }}</p>
                </div>
            </div>

            {{-- Members --}}
            <div class="space-y-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Anggota</p>
                @foreach($myGroup->members as $member)
                @php
                    $memberUser = \App\Models\User::find($member->student_user_id);
                    $memberSiswa = \App\Models\Siswa::where('user_id', $member->student_user_id)->first();
                @endphp
                <div class="flex items-center gap-3 py-2 border-b border-gray-50">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                        {{ strtoupper(substr($memberSiswa?->nama ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700">{{ $memberSiswa?->nama ?? $memberUser?->username }}</p>
                        @if($myGroup->isLeader($member->student_user_id))
                            <span class="text-xs text-yellow-600 font-medium"><i class="ph ph-crown mr-0.5"></i>Ketua</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Poster Upload / Status --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Poster</h3>
        </div>
        <div class="p-5">
            @if($myPoster)
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="ph ph-image text-2xl text-purple-500"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-800">{{ $myPoster->original_name }}</p>
                    <p class="text-xs text-gray-400">{{ $myPoster->getFileSizeFormatted() }} · {{ $myPoster->uploaded_at?->format('d M Y H:i') }}</p>
                </div>
                <span class="ml-auto px-3 py-1 text-xs font-bold rounded-full
                    {{ $myPoster->status === 'approved' ? 'bg-green-100 text-green-700' :
                       ($myPoster->status === 'submitted' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                    {{ ucfirst($myPoster->status) }}
                </span>
            </div>
            <a href="{{ $myPoster->getPublicUrl() }}" target="_blank"
                class="inline-flex items-center gap-2 text-sm text-brand-teal hover:underline">
                <i class="ph ph-eye"></i> Lihat Poster
            </a>
            @endif

            @if($isLeader && $systemStatus === 'open')
            <form method="POST" action="{{ route('siswa.poster.upload') }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                @csrf
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-brand-teal transition-colors cursor-pointer" onclick="document.getElementById('poster-file').click()">
                    <i class="ph ph-upload-simple text-3xl text-gray-400 mb-2 block"></i>
                    <p class="text-sm font-medium text-gray-600">{{ $myPoster ? 'Ganti Poster' : 'Upload Poster' }}</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP, atau PDF. Maks 10MB.</p>
                    <input type="file" id="poster-file" name="poster" accept=".jpg,.jpeg,.png,.webp,.pdf" class="hidden" onchange="updateFileName(this)">
                </div>
                <p id="file-name" class="text-xs text-gray-500 hidden"></p>
                <button type="submit" class="w-full py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl font-semibold text-sm transition-colors">
                    <i class="ph ph-paper-plane-tilt mr-2"></i> Upload Poster
                </button>
            </form>
            @elseif(!$isLeader)
            <div class="mt-4 p-4 bg-gray-50 rounded-xl text-center text-gray-500 text-sm">
                <i class="ph ph-lock-key text-lg mb-1 block text-gray-400"></i>
                Hanya ketua kelompok yang dapat mengupload poster.
            </div>
            @elseif($systemStatus === 'closed')
            <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-xl text-orange-700 text-sm">
                <i class="ph ph-lock-key mr-2"></i> Sistem sedang ditutup. Upload poster tidak tersedia.
            </div>
            @endif
        </div>
    </div>


    {{-- Quick Actions for Leader --}}
    @if($isLeader && $myGroup)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Tugas Ketua Kelompok</h3>
            <p class="text-xs text-gray-400 mt-0.5">Sebagai ketua, Anda memiliki tanggung jawab tambahan</p>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
            <a href="{{ route('siswa.product_metadata.index') }}"
               class="flex items-center gap-4 p-4 bg-amber-50 hover:bg-amber-100 rounded-xl transition-colors border border-amber-100">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                    <i class="ph ph-cooking-pot text-xl text-amber-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-sm text-amber-800">Metadata Produk</p>
                    <p class="text-xs text-amber-600 mt-0.5">
                        @if($myGroup->productMetadata)
                            <i class="ph ph-check-circle"></i> Sudah diisi: {{ $myGroup->productMetadata->product_name }}
                        @else
                            Belum diisi
                        @endif
                    </p>
                </div>
                <i class="ph ph-arrow-right text-amber-400 ml-auto"></i>
            </a>
            <a href="{{ route('siswa.contribution.index') }}"
               class="flex items-center gap-4 p-4 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors border border-indigo-100">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0">
                    <i class="ph ph-users-three text-xl text-indigo-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-sm text-indigo-800">Kontribusi Anggota</p>
                    <p class="text-xs text-indigo-600 mt-0.5">
                        @php $contribSubmitted = \App\Models\ScfMemberContribution::isSubmittedForGroup($myGroup->id); @endphp
                        @if($contribSubmitted)
                            <i class="ph ph-check-circle"></i> Sudah disubmit
                        @else
                            Belum disubmit
                        @endif
                    </p>
                </div>
                <i class="ph ph-arrow-right text-indigo-400 ml-auto"></i>
            </a>
        </div>
    </div>
    @endif

    @else
    {{-- No group --}}
    <div class="bg-white rounded-2xl p-10 border border-gray-100 text-center shadow-card text-gray-400">
        <i class="ph ph-users text-4xl mb-3 block opacity-40"></i>
        <p class="font-medium text-gray-600">Anda belum terdaftar dalam kelompok.</p>
        <p class="text-sm mt-1">Hubungi guru pembimbing Anda.</p>
    </div>
    @endif

    {{-- Documents --}}
    @if($documents->count() > 0)
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <h3 class="font-bold text-gray-800 mb-3">Dokumen Program</h3>
        <div class="space-y-2">
            @foreach($documents as $doc)
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <i class="ph ph-book-open-text text-brand-teal text-lg"></i>
                <div>
                    <span class="text-xs font-bold uppercase {{ $doc->type === 'juklak' ? 'text-blue-600' : 'text-purple-600' }}">{{ $doc->type }}</span>
                    <p class="text-sm font-medium text-gray-700">{{ $doc->title }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function updateFileName(input) {
    const label = document.getElementById('file-name');
    if (input.files.length > 0) {
        label.textContent = 'File dipilih: ' + input.files[0].name;
        label.classList.remove('hidden');
    }
}
</script>
@endpush
