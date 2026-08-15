@extends('layouts.app')

@section('title', 'Pengaturan Nilai SCF')
@section('page-title', 'Pengaturan Nilai SCF')

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
    <a href="{{ route('operator.score_settings.index') }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl text-gray-600 hover:bg-brand-teal-light hover:text-brand-teal font-medium transition-colors">
        <i class="ph ph-sliders text-xl"></i> Pengaturan Nilai
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Section 1: Bobot Komponen Nilai Kelompok --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-800">Bobot Komponen Nilai Kelompok</h3>
            <p class="text-sm text-gray-500">Atur bobot untuk masing-masing komponen penilaian. Total harus 100%.</p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4 text-yellow-700 text-sm flex gap-2 items-start">
            <i class="ph ph-warning-circle text-lg mt-0.5"></i>
            <p>Bobot harus berjumlah 100%. Jika belum dikonfigurasi, sistem menggunakan equal weight (33.33%)</p>
        </div>

        <form action="{{ route('operator.score_settings.weights') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bobot Poster (%)</label>
                    <input type="number" step="0.01" name="poster_weight" id="poster_weight" 
                        class="weight-input w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal focus:border-brand-teal focus:bg-white transition-all text-sm" 
                        value="{{ $settings['poster_weight'] ?? '' }}" placeholder="Belum dikonfigurasi" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bobot IPA/Science (%)</label>
                    <input type="number" step="0.01" name="science_weight" id="science_weight" 
                        class="weight-input w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal focus:border-brand-teal focus:bg-white transition-all text-sm" 
                        value="{{ $settings['science_weight'] ?? '' }}" placeholder="Belum dikonfigurasi" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bobot Makanan/Food (%)</label>
                    <input type="number" step="0.01" name="food_weight" id="food_weight" 
                        class="weight-input w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal focus:border-brand-teal focus:bg-white transition-all text-sm" 
                        value="{{ $settings['food_weight'] ?? '' }}" placeholder="Belum dikonfigurasi" required>
                </div>
            </div>

            <div class="flex items-center justify-between mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-700">Total Bobot:</span>
                    <span id="total_weight_display" class="text-lg font-bold">0%</span>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-brand-teal hover:bg-teal-600 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                    Simpan Bobot
                </button>
            </div>
        </form>
    </div>

    {{-- Section 2: Contribution Factor --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-800">Contribution Factor (Faktor Kontribusi)</h3>
            <p class="text-sm text-gray-500">Pengali nilai akhir berdasarkan tingkat kontribusi peserta dalam kelompok.</p>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 text-blue-700 text-sm flex gap-2 items-start">
            <i class="ph ph-info text-lg mt-0.5"></i>
            <p>REDUCED dan NONE belum ditentukan secara resmi. Kosongkan jika belum ingin menggunakan.</p>
        </div>

        <form action="{{ route('operator.score_settings.contribution_factors') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Contribution</label>
                    <input type="number" step="0.01" name="contribution_factor_full" 
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal focus:border-brand-teal focus:bg-white transition-all text-sm" 
                        value="{{ $settings['contribution_factor_full'] ?? '1.00' }}">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Reduced Contribution</label>
                    <input type="number" step="0.01" name="contribution_factor_reduced" 
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal focus:border-brand-teal focus:bg-white transition-all text-sm" 
                        value="{{ $settings['contribution_factor_reduced'] ?? '' }}" placeholder="Opsional">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">No Contribution</label>
                    <input type="number" step="0.01" name="contribution_factor_none" 
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal focus:border-brand-teal focus:bg-white transition-all text-sm" 
                        value="{{ $settings['contribution_factor_none'] ?? '' }}" placeholder="Opsional">
                </div>
            </div>

            <div class="flex justify-end mt-4">
                <button type="submit" class="px-5 py-2.5 bg-brand-teal hover:bg-teal-600 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm">
                    Simpan Faktor Kontribusi
                </button>
            </div>
        </form>
    </div>

    {{-- Section 3: Daftar Kriteria Penilaian --}}
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-800">Daftar Kriteria Penilaian</h3>
            <p class="text-sm text-gray-500">Atur kriteria penilaian untuk setiap komponen.</p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 text-yellow-700 text-sm flex gap-2 items-start">
            <i class="ph ph-warning text-lg mt-0.5"></i>
            <p>Kriteria IPA dan Makanan sudah ditetapkan. Ubah dengan hati-hati.</p>
        </div>

        {{-- Tabs Header --}}
        <div class="flex border-b border-gray-200 mb-4 overflow-x-auto">
            <button type="button" class="tab-btn px-6 py-3 font-semibold text-sm border-b-2 border-brand-teal text-brand-teal whitespace-nowrap" data-target="tab-poster">
                <i class="ph ph-image mr-1"></i> Poster
            </button>
            <button type="button" class="tab-btn px-6 py-3 font-semibold text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-target="tab-science">
                <i class="ph ph-flask mr-1"></i> IPA/Science
            </button>
            <button type="button" class="tab-btn px-6 py-3 font-semibold text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-target="tab-food">
                <i class="ph ph-cooking-pot mr-1"></i> Makanan/Food
            </button>
        </div>

        {{-- Tabs Content --}}
        <div class="tab-contents">
            @foreach(['poster' => 'Poster', 'science' => 'IPA/Science', 'food' => 'Makanan/Food'] as $key => $label)
            <div id="tab-{{ $key }}" class="tab-pane {{ $key !== 'poster' ? 'hidden' : '' }}">
                <div class="space-y-4">
                    @forelse($criteria[$key] ?? [] as $criterion)
                    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 hover:border-brand-teal transition-colors">
                        <form action="{{ route('operator.score_settings.criteria.update', $criterion->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
                                <div class="lg:col-span-5">
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Nama Kriteria</label>
                                    <input type="text" name="name" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm mb-2" value="{{ $criterion->name }}" required>
                                    
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Deskripsi</label>
                                    <textarea name="description" rows="2" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm">{{ $criterion->description }}</textarea>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Bobot</label>
                                    <input type="number" step="0.01" name="weight" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm" value="{{ $criterion->weight }}" required>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Skor Maksimal</label>
                                    <input type="number" step="1" name="max_score" class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm" value="{{ $criterion->max_score }}" required>
                                </div>
                                <div class="lg:col-span-2 flex flex-col justify-center h-full pt-4">
                                    <label class="flex items-center cursor-pointer gap-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 text-brand-teal rounded focus:ring-brand-teal" {{ $criterion->is_active ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-gray-700">Aktif</span>
                                    </label>
                                </div>
                                <div class="lg:col-span-1 flex justify-end h-full pt-4">
                                    <button type="submit" class="p-2 bg-white border border-brand-teal text-brand-teal hover:bg-brand-teal hover:text-white rounded-lg transition-colors" title="Simpan Perubahan">
                                        <i class="ph ph-floppy-disk text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @empty
                    <div class="text-center py-6 text-gray-500">
                        <i class="ph ph-folder-open text-3xl mb-2"></i>
                        <p>Belum ada kriteria untuk {{ $label }}</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Live calculation for weights
    const weightInputs = document.querySelectorAll('.weight-input');
    const displayTotal = document.getElementById('total_weight_display');

    function updateTotal() {
        let total = 0;
        weightInputs.forEach(input => {
            const val = parseFloat(input.value);
            if (!isNaN(val)) total += val;
        });
        
        displayTotal.innerText = total.toFixed(2) + '%';
        
        if (Math.abs(total - 100) < 0.01) {
            displayTotal.classList.remove('text-red-500');
            displayTotal.classList.add('text-green-600');
        } else {
            displayTotal.classList.remove('text-green-600');
            displayTotal.classList.add('text-red-500');
        }
    }

    weightInputs.forEach(input => {
        input.addEventListener('input', updateTotal);
    });
    
    // Initial calculate
    updateTotal();

    // Tabs functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active classes
            tabBtns.forEach(b => {
                b.classList.remove('border-brand-teal', 'text-brand-teal');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            tabPanes.forEach(p => p.classList.add('hidden'));

            // Add active class to clicked
            btn.classList.remove('border-transparent', 'text-gray-500');
            btn.classList.add('border-brand-teal', 'text-brand-teal');
            
            const target = btn.getAttribute('data-target');
            document.getElementById(target).classList.remove('hidden');
        });
    });
</script>
@endpush
