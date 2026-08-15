<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PanduanController extends Controller
{
    public function show(?string $filename = null): View
    {
        if (empty($filename)) {
            $filename = 'panduan-projek-ipa.md';
        }

        // Sanitize filename to prevent path traversal
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
            // Fallback check if default file exists anywhere
            $defaultPath = base_path('panduan-projek-ipa.md');
            if (File::exists($defaultPath)) {
                $filePath = $defaultPath;
                $cleanFilename = 'panduan-projek-ipa.md';
            } else {
                abort(404, 'Dokumen panduan tidak ditemukan.');
            }
        }

        $rawContent = File::get($filePath);

        // Render Markdown to HTML using Laravel's Str::markdown
        $htmlContent = Str::markdown($rawContent);

        // Generate Table of Contents
        $toc = $this->extractTableOfContents($rawContent);

        // List available markdown files in root and markdown/ folder
        $availableFiles = $this->getAvailableMarkdownFiles();

        return view('panduan.show', compact(
            'cleanFilename',
            'rawContent',
            'htmlContent',
            'toc',
            'availableFiles'
        ));
    }

    private function extractTableOfContents(string $markdown): array
    {
        $toc = [];
        $lines = explode("\n", $markdown);

        foreach ($lines as $line) {
            if (preg_match('/^(#{1,3})\s+(.+)$/', trim($line), $matches)) {
                $level = strlen($matches[1]);
                $title = trim(strip_tags($matches[2]));

                // Clean markdown formatting like **bold** or *italic* from TOC titles
                $titleClean = preg_replace('/[*_`#]/', '', $title);
                $id = Str::slug($titleClean);

                if (!empty($titleClean)) {
                    $toc[] = [
                        'level' => $level,
                        'title' => $titleClean,
                        'id'    => $id,
                    ];
                }
            }
        }

        return $toc;
    }

    private function getAvailableMarkdownFiles(): array
    {
        $files = [];

        // Check markdown/ folder
        $markdownDir = base_path('markdown');
        if (File::exists($markdownDir)) {
            foreach (File::files($markdownDir) as $file) {
                if ($file->getExtension() === 'md') {
                    $files[$file->getFilename()] = Str::headline($file->getFilenameWithoutExtension());
                }
            }
        }

        // Check root project folder for .md files
        foreach (File::files(base_path()) as $file) {
            if ($file->getExtension() === 'md' && !in_array($file->getFilename(), ['README.md', 'LICENSE.md'])) {
                $files[$file->getFilename()] = Str::headline($file->getFilenameWithoutExtension());
            }
        }

        return $files;
    }
}
