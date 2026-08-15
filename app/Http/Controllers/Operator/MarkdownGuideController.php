<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\ScfProgram;
use App\Models\ScfActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MarkdownGuideController extends Controller
{
    public function index()
    {
        return redirect()->route('operator.markdown.edit', ['filename' => 'panduan-projek-ipa.md']);
    }

    public function edit(?string $filename = null): View
    {
        if (empty($filename)) {
            $filename = 'panduan-projek-ipa.md';
        }

        $cleanFilename = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '.md';

        $possiblePaths = [
            base_path('markdown/' . $cleanFilename),
            base_path($cleanFilename),
        ];

        $filePath = null;
        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                $filePath = $path;
                break;
            }
        }

        if (!$filePath) {
            $defaultPath = base_path('panduan-projek-ipa.md');
            if (File::exists($defaultPath)) {
                $filePath = $defaultPath;
                $cleanFilename = 'panduan-projek-ipa.md';
            } else {
                // Initialize default empty file if not exists
                $filePath = base_path('markdown/' . $cleanFilename);
                $markdownDir = base_path('markdown');
                if (!File::exists($markdownDir)) {
                    File::makeDirectory($markdownDir, 0755, true);
                }
                File::put($filePath, "# Judul Dokumen\n\nIsi konten markdown...");
            }
        }

        $rawContent = File::get($filePath);
        $availableFiles = $this->getAvailableMarkdownFiles();

        return view('operator.markdown_editor', compact(
            'cleanFilename',
            'rawContent',
            'availableFiles'
        ));
    }

    public function save(Request $request)
    {
        $request->validate([
            'filename' => ['required', 'string'],
            'content'  => ['required', 'string'],
        ], [
            'filename.required' => 'Nama file markdown wajib diisi.',
            'content.required'  => 'Konten markdown wajib diisi.',
        ]);

        $cleanFilename = Str::slug(pathinfo($request->filename, PATHINFO_FILENAME)) . '.md';

        $markdownDir = base_path('markdown');
        if (!File::exists($markdownDir)) {
            File::makeDirectory($markdownDir, 0755, true);
        }

        $markdownPath = base_path('markdown/' . $cleanFilename);
        $rootPath = base_path($cleanFilename);

        // Save content to both markdown/ folder and root file
        File::put($markdownPath, $request->content);
        File::put($rootPath, $request->content);

        $program = ScfProgram::getActive();
        ScfActivityLog::log(
            Auth::id(),
            'markdown_updated',
            "Operator memperbarui dokumen Markdown: {$cleanFilename}.",
            $program?->id
        );

        return back()->with('success', "Dokumen Markdown ({$cleanFilename}) berhasil diperbarui.");
    }

    public function create(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ], [
            'title.required' => 'Judul dokumen markdown baru wajib diisi.',
        ]);

        $cleanFilename = Str::slug($request->title) . '.md';
        $markdownDir = base_path('markdown');
        if (!File::exists($markdownDir)) {
            File::makeDirectory($markdownDir, 0755, true);
        }

        $markdownPath = base_path('markdown/' . $cleanFilename);
        $rootPath = base_path($cleanFilename);

        if (!File::exists($markdownPath)) {
            $initialContent = "# " . strtoupper($request->title) . "\n\nDokumen panduan baru dibuat oleh Operator pada " . now()->format('d M Y') . ".\n";
            File::put($markdownPath, $initialContent);
            File::put($rootPath, $initialContent);
        }

        return redirect()->route('operator.markdown.edit', ['filename' => $cleanFilename])
            ->with('success', "Berkas markdown {$cleanFilename} berhasil dibuat.");
    }

    private function getAvailableMarkdownFiles(): array
    {
        $files = [];

        $markdownDir = base_path('markdown');
        if (File::exists($markdownDir)) {
            foreach (File::files($markdownDir) as $file) {
                if ($file->getExtension() === 'md') {
                    $files[$file->getFilename()] = Str::headline($file->getFilenameWithoutExtension());
                }
            }
        }

        foreach (File::files(base_path()) as $file) {
            if ($file->getExtension() === 'md' && !in_array($file->getFilename(), ['README.md', 'LICENSE.md'])) {
                $files[$file->getFilename()] = Str::headline($file->getFilenameWithoutExtension());
            }
        }

        return $files;
    }
}
