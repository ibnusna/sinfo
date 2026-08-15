<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfDocument;
use App\Models\ScfActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index()
    {
        return redirect()->route('operator.markdown.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'    => ['required', 'in:juklak,juknis'],
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ], [
            'type.required'    => 'Pilih jenis dokumen (Juklak/Juknis).',
            'title.required'   => 'Judul dokumen wajib diisi.',
            'content.required' => 'Konten dokumen wajib diisi.',
        ]);

        $program = ScfProgram::getActive();
        if (!$program) {
            return back()->with('error', 'Tidak ada program SCF yang aktif.');
        }

        $document = ScfDocument::create([
            'program_id' => $program->id,
            'type'       => $request->type,
            'title'      => $request->title,
            'content'    => $request->content,
            'status'     => 'draft',
            'created_by' => Auth::id(),
        ]);

        $typeLabel = strtoupper($request->type);

        ScfActivityLog::log(
            Auth::id(),
            'document_created',
            "Operator membuat {$typeLabel}: {$request->title}.",
            $program->id
        );

        return back()->with('success', "{$typeLabel} berhasil dibuat.");
    }

    public function update(Request $request, int $id)
    {
        $document = ScfDocument::findOrFail($id);

        $request->validate([
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $document->update([
            'title'   => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function publish(int $id)
    {
        $document = ScfDocument::findOrFail($id);

        $document->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $typeLabel = strtoupper($document->type);
        $program = ScfProgram::getActive();

        ScfActivityLog::log(
            Auth::id(),
            'document_published',
            "Operator mempublish {$typeLabel}: {$document->title}.",
            $program?->id
        );

        return back()->with('success', "{$typeLabel} berhasil dipublish.");
    }

    public function destroy(int $id)
    {
        $document = ScfDocument::findOrFail($id);
        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
