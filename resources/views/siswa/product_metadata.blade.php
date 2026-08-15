@extends('layouts.app')

@section('title', 'Metadata Produk')
@section('page-title', 'Metadata Produk')

@section('nav-menu')
    <a href="{{ route('siswa.dashboard') }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors text-gray-600 hover:bg-gray-50">
        <i class="ph ph-squares-four text-xl"></i> Dashboard
    </a>
    <a href="{{ route('siswa.product_metadata.index') ?? '#' }}" class="nav-link active flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors">
        <i class="ph ph-info text-xl"></i> Metadata Produk
    </a>
    <a href="{{ route('siswa.contribution.index') ?? '#' }}" class="nav-link flex items-center gap-3 px-3 py-3 rounded-xl font-medium transition-colors text-gray-600 hover:bg-gray-50">
        <i class="ph ph-users-three text-xl"></i> Kontribusi
    </a>
@endsection

@section('content')
<div class="space-y-6 slide-up">

    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3 text-blue-700">
        <i class="ph ph-info text-xl shrink-0"></i>
        <div>
            <p class="text-sm">Data ini dapat dilihat oleh Guru dan Juri sebagai referensi.</p>
        </div>
    </div>

    @if(!$isLeader)
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-card text-center text-gray-500">
            <i class="ph ph-lock-key text-3xl mb-2 block text-gray-400"></i>
            <p>Hanya ketua kelompok yang dapat mengisi metadata produk.</p>
        </div>
    @endif

    @if($metadata)
        {{-- Show current metadata in read-only card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">Metadata Produk Saat Ini</h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase">Nama Produk</h4>
                    <p class="text-gray-800 font-medium mt-1">{{ $metadata->product_name }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase">Deskripsi Singkat</h4>
                    <p class="text-gray-800 mt-1 text-sm">{{ $metadata->description ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase">Bahan-Bahan Utama</h4>
                    <p class="text-gray-800 mt-1 text-sm">{{ $metadata->ingredients ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Kandungan Gizi</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-xs text-gray-500 block">Karbohidrat</span>
                            <span class="font-semibold text-gray-800">{{ $metadata->carbohydrate ?? '-' }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-xs text-gray-500 block">Protein</span>
                            <span class="font-semibold text-gray-800">{{ $metadata->protein ?? '-' }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-xs text-gray-500 block">Lemak</span>
                            <span class="font-semibold text-gray-800">{{ $metadata->fat ?? '-' }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-xs text-gray-500 block">Lainnya</span>
                            <span class="font-semibold text-gray-800">{{ $metadata->other_nutrients ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($isLeader)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-card overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-800">{{ $metadata ? 'Update Metadata Produk' : 'Isi Metadata Produk' }}</h3>
            </div>
            <div class="p-5">
                <form action="{{ route('siswa.product_metadata.store') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" id="product_name" name="product_name" required value="{{ old('product_name', $metadata->product_name ?? '') }}" class="w-full rounded-xl border-gray-300 focus:border-brand-teal focus:ring-brand-teal text-sm">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                        <textarea id="description" name="description" rows="3" class="w-full rounded-xl border-gray-300 focus:border-brand-teal focus:ring-brand-teal text-sm">{{ old('description', $metadata->description ?? '') }}</textarea>
                    </div>

                    <div>
                        <label for="ingredients" class="block text-sm font-medium text-gray-700 mb-1">Bahan-Bahan Utama</label>
                        <textarea id="ingredients" name="ingredients" rows="3" class="w-full rounded-xl border-gray-300 focus:border-brand-teal focus:ring-brand-teal text-sm">{{ old('ingredients', $metadata->ingredients ?? '') }}</textarea>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Kandungan Gizi</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="carbohydrate" class="block text-xs font-medium text-gray-600 mb-1">Karbohidrat</label>
                                <input type="text" id="carbohydrate" name="carbohydrate" value="{{ old('carbohydrate', $metadata->carbohydrate ?? '') }}" placeholder="Contoh: 20g" class="w-full rounded-lg border-gray-300 focus:border-brand-teal focus:ring-brand-teal text-sm">
                            </div>
                            <div>
                                <label for="protein" class="block text-xs font-medium text-gray-600 mb-1">Protein</label>
                                <input type="text" id="protein" name="protein" value="{{ old('protein', $metadata->protein ?? '') }}" placeholder="Contoh: 10g" class="w-full rounded-lg border-gray-300 focus:border-brand-teal focus:ring-brand-teal text-sm">
                            </div>
                            <div>
                                <label for="fat" class="block text-xs font-medium text-gray-600 mb-1">Lemak</label>
                                <input type="text" id="fat" name="fat" value="{{ old('fat', $metadata->fat ?? '') }}" placeholder="Contoh: 5g" class="w-full rounded-lg border-gray-300 focus:border-brand-teal focus:ring-brand-teal text-sm">
                            </div>
                            <div>
                                <label for="other_nutrients" class="block text-xs font-medium text-gray-600 mb-1">Kandungan Lainnya</label>
                                <input type="text" id="other_nutrients" name="other_nutrients" value="{{ old('other_nutrients', $metadata->other_nutrients ?? '') }}" placeholder="Contoh: Vitamin C" class="w-full rounded-lg border-gray-300 focus:border-brand-teal focus:ring-brand-teal text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-brand-teal hover:bg-brand-teal-dark text-white rounded-xl font-semibold text-sm transition-colors shadow-soft">
                            <i class="ph ph-floppy-disk mr-2"></i> Simpan Metadata
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
