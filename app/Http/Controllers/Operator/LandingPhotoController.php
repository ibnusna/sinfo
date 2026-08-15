<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ScfLandingPhoto;
use App\Models\ScfProgram;
use App\Models\ScfActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class LandingPhotoController extends Controller
{
    public function index(): View
    {
        $photos = ScfLandingPhoto::orderBy('section')
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->get();

        $heroPhotos = $photos->where('section', 'hero');
        $aboutPhotos = $photos->where('section', 'about');
        $galleryPhotos = $photos->where('section', 'gallery');

        return view('operator.landing_photos', compact(
            'photos',
            'heroPhotos',
            'aboutPhotos',
            'galleryPhotos'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'section'    => ['required', 'in:hero,about,gallery'],
            'category'   => ['nullable', 'string', 'max:50'],
            'image'      => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp,svg', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [
            'title.required'   => 'Judul foto wajib diisi.',
            'section.required' => 'Pilih posisi/seksi foto.',
            'image.required'   => 'Berkas gambar foto wajib diunggah.',
            'image.image'      => 'Berkas harus berupa gambar valid.',
            'image.max'        => 'Ukuran foto maksimal 5 MB.',
        ]);

        $imagePath = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadDir = public_path('foto/uploads');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $imagePath = 'foto/uploads/' . $filename;
        }

        $photo = ScfLandingPhoto::create([
            'title'      => $request->title,
            'section'    => $request->section,
            'category'   => $request->category ?: ($request->section === 'gallery' ? 'exhibition' : null),
            'image_path' => $imagePath,
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => true,
        ]);

        $program = ScfProgram::getActive();
        ScfActivityLog::log(
            Auth::id(),
            'landing_photo_created',
            "Operator menambahkan foto landing page: {$photo->title} ({$photo->section}).",
            $program?->id
        );

        return back()->with('success', 'Foto landing page berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $photo = ScfLandingPhoto::findOrFail($id);

        $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'section'    => ['required', 'in:hero,about,gallery'],
            'category'   => ['nullable', 'string', 'max:50'],
            'image'      => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp,svg', 'max:5120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data = [
            'title'      => $request->title,
            'section'    => $request->section,
            'category'   => $request->category ?: ($request->section === 'gallery' ? 'exhibition' : null),
            'sort_order' => $request->sort_order ?? $photo->sort_order,
            'is_active'  => $request->has('is_active') ? (bool) $request->is_active : $photo->is_active,
        ];

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $uploadDir = public_path('foto/uploads');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);

            // Optionally delete old upload file if in uploads directory
            if (str_contains($photo->image_path, 'foto/uploads/') && File::exists(public_path($photo->image_path))) {
                File::delete(public_path($photo->image_path));
            }

            $data['image_path'] = 'foto/uploads/' . $filename;
        }

        $photo->update($data);

        $program = ScfProgram::getActive();
        ScfActivityLog::log(
            Auth::id(),
            'landing_photo_updated',
            "Operator memperbarui foto landing page: {$photo->title}.",
            $program?->id
        );

        return back()->with('success', 'Foto landing page berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $photo = ScfLandingPhoto::findOrFail($id);

        if (str_contains($photo->image_path, 'foto/uploads/') && File::exists(public_path($photo->image_path))) {
            File::delete(public_path($photo->image_path));
        }

        $photo->delete();

        $program = ScfProgram::getActive();
        ScfActivityLog::log(
            Auth::id(),
            'landing_photo_deleted',
            "Operator menghapus foto landing page: {$photo->title}.",
            $program?->id
        );

        return back()->with('success', 'Foto landing page berhasil dihapus.');
    }
}
