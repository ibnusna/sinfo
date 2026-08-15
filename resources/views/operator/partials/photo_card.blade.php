<div class="bg-gray-50/80 rounded-2xl border border-gray-200/80 overflow-hidden group hover:shadow-card transition-all duration-300 flex flex-col justify-between">
    <div>
        {{-- Image Thumbnail Container --}}
        <div class="relative w-full aspect-video bg-gray-200 overflow-hidden">
            <img src="{{ $photo->url }}" alt="{{ $photo->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            
            <div class="absolute top-3 left-3 flex items-center gap-1.5">
                <span class="px-2.5 py-1 bg-black/60 backdrop-blur-md text-white text-[11px] font-bold rounded-lg uppercase">
                    {{ $photo->section }}
                </span>
                @if($photo->section === 'gallery' && $photo->category)
                    <span class="px-2.5 py-1 bg-brand-teal/80 backdrop-blur-md text-white text-[11px] font-semibold rounded-lg capitalize">
                        {{ $photo->category }}
                    </span>
                @endif
            </div>

            <div class="absolute top-3 right-3">
                @if($photo->is_active)
                    <span class="px-2 py-0.5 bg-green-500/90 text-white text-[10px] font-bold rounded-md shadow-sm">Aktif</span>
                @else
                    <span class="px-2 py-0.5 bg-gray-500/90 text-white text-[10px] font-bold rounded-md shadow-sm">Non-aktif</span>
                @endif
            </div>
        </div>

        {{-- Details --}}
        <div class="p-4">
            <h5 class="font-bold text-gray-800 text-sm line-clamp-1 group-hover:text-brand-teal transition-colors">{{ $photo->title }}</h5>
            <div class="flex items-center justify-between text-xs text-gray-400 mt-2">
                <span>Urutan: #{{ $photo->sort_order }}</span>
                <span>{{ $photo->created_at?->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Action Footer --}}
    <div class="px-4 py-3 bg-white border-t border-gray-100 flex items-center justify-between">
        <button onclick="openEditModal({{ json_encode($photo) }})" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-brand-teal transition-colors">
            <i class="ph ph-note-pencil text-base"></i> Edit
        </button>

        <form method="POST" action="{{ route('operator.landing_photos.destroy', $photo->id) }}" class="inline">
            @csrf
            @method('DELETE')
            <button type="button" onclick="confirmDeletePhoto(this.closest('form'))" class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                <i class="ph ph-trash text-base"></i> Hapus
            </button>
        </form>
    </div>
</div>
