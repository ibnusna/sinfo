@php
    $user = auth()->user();
    $role = $user ? $user->getScfRole() : 'guest';
@endphp

@if($role === 'operator' || $role === 'super_admin')
    <a href="{{ route('operator.dashboard') }}"
       class="nav-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-squares-four text-xl"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('operator.assignments.index') }}"
       class="nav-link {{ request()->routeIs('operator.assignments.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-users-three text-xl"></i>
        <span>Manajemen Guru & Juri</span>
    </a>
    <a href="{{ route('operator.markdown.index') }}"
       class="nav-link {{ request()->routeIs('operator.markdown.*') || request()->routeIs('operator.documents.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-book-open-text text-xl"></i>
        <span>Panduan Projek (Markdown)</span>
    </a>
    <a href="{{ route('operator.landing_photos.index') }}"
       class="nav-link {{ request()->routeIs('operator.landing_photos.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-image text-xl"></i>
        <span>Foto Landing Page</span>
    </a>
    <a href="{{ route('operator.score_settings.index') }}"
       class="nav-link {{ request()->routeIs('operator.score_settings.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-sliders text-xl"></i>
        <span>Konfigurasi Penilaian</span>
    </a>
    <div class="pt-3 mt-3 border-t border-gray-100">
        <a href="{{ route('panduan.show') }}"
           class="nav-link {{ request()->routeIs('panduan.show') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
            <i class="ph ph-book-bookmark text-xl text-brand-teal"></i>
            <span>Panduan Projek IPA</span>
        </a>
    </div>

@elseif($role === 'guru' || $role === 'guru_unassigned')
    <a href="{{ route('guru.dashboard') }}"
       class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-squares-four text-xl"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('guru.groups.index') }}"
       class="nav-link {{ request()->routeIs('guru.groups.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-users text-xl"></i>
        <span>Manajemen Kelompok</span>
    </a>
    <a href="{{ route('guru.assessments.index') }}"
       class="nav-link {{ request()->routeIs('guru.assessments.*') || request()->routeIs('guru.scores.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-clipboard-text text-xl"></i>
        <span>Penilaian IPA</span>
    </a>
    <div class="pt-3 mt-3 border-t border-gray-100">
        <a href="{{ route('panduan.show') }}"
           class="nav-link {{ request()->routeIs('panduan.show') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
            <i class="ph ph-book-bookmark text-xl text-brand-teal"></i>
            <span>Panduan Projek IPA</span>
        </a>
    </div>

@elseif($role === 'juri')
    <a href="{{ route('juri.dashboard') }}"
       class="nav-link {{ request()->routeIs('juri.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-squares-four text-xl"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('juri.assessments.index') }}"
       class="nav-link {{ request()->routeIs('juri.assessments.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-star text-xl"></i>
        <span>Penilaian Juri</span>
    </a>
    <div class="pt-3 mt-3 border-t border-gray-100">
        <a href="{{ route('panduan.show') }}"
           class="nav-link {{ request()->routeIs('panduan.show') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
            <i class="ph ph-book-bookmark text-xl text-brand-teal"></i>
            <span>Panduan Projek IPA</span>
        </a>
    </div>

@elseif($role === 'siswa')
    <a href="{{ route('siswa.dashboard') }}"
       class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-squares-four text-xl"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('siswa.product_metadata.index') }}"
       class="nav-link {{ request()->routeIs('siswa.product_metadata.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-cooking-pot text-xl"></i>
        <span>Metadata Produk</span>
    </a>
    <a href="{{ route('siswa.contribution.index') }}"
       class="nav-link {{ request()->routeIs('siswa.contribution.*') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-users-three text-xl"></i>
        <span>Kontribusi Anggota</span>
    </a>
    <div class="pt-3 mt-3 border-t border-gray-100">
        <a href="{{ route('panduan.show') }}"
           class="nav-link {{ request()->routeIs('panduan.show') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
            <i class="ph ph-book-bookmark text-xl text-brand-teal"></i>
            <span>Panduan Projek IPA</span>
        </a>
    </div>

@elseif($role === 'kepsek')
    <a href="{{ route('kepsek.dashboard') }}"
       class="nav-link {{ request()->routeIs('kepsek.dashboard') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
        <i class="ph ph-squares-four text-xl"></i>
        <span>Dashboard</span>
    </a>
    <div class="pt-3 mt-3 border-t border-gray-100">
        <a href="{{ route('panduan.show') }}"
           class="nav-link {{ request()->routeIs('panduan.show') ? 'active' : '' }} flex items-center gap-3 px-3.5 py-3 rounded-xl font-medium transition-colors text-gray-700 hover:bg-brand-teal-light hover:text-brand-teal">
            <i class="ph ph-book-bookmark text-xl text-brand-teal"></i>
            <span>Panduan Projek IPA</span>
        </a>
    </div>
@endif
