<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboard;
use App\Http\Controllers\Operator\AssignmentController;
use App\Http\Controllers\Operator\DocumentController;
use App\Http\Controllers\Operator\ScoreSettingController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboard;
use App\Http\Controllers\Guru\GroupController;
use App\Http\Controllers\Guru\AssessmentController as GuruAssessment;
use App\Http\Controllers\Guru\ScoreController as GuruScore;
use App\Http\Controllers\Guru\FinalizeController;
use App\Http\Controllers\Juri\DashboardController as JuriDashboard;
use App\Http\Controllers\Juri\AssessmentController as JuriAssessment;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboard;
use App\Http\Controllers\Siswa\PosterController;
use App\Http\Controllers\Siswa\ProductMetadataController;
use App\Http\Controllers\Siswa\ContributionController;
use App\Http\Controllers\KepsekController;

use App\Http\Controllers\Operator\LandingPhotoController;
use App\Http\Controllers\Operator\MarkdownGuideController;
use App\Http\Controllers\PanduanController;

// ─────────────────────────────────────────────────
// LANDING PAGE — Foto Dinamis dari Operator
// ─────────────────────────────────────────────────
Route::get('/', function () {
    $heroPhoto = \App\Models\ScfLandingPhoto::where('section', 'hero')->where('is_active', true)->orderBy('sort_order')->first();
    $aboutPhoto = \App\Models\ScfLandingPhoto::where('section', 'about')->where('is_active', true)->orderBy('sort_order')->first();
    $galleryPhotos = \App\Models\ScfLandingPhoto::where('section', 'gallery')->where('is_active', true)->orderBy('sort_order')->orderBy('id', 'desc')->get();

    return view('landing', compact('heroPhoto', 'aboutPhoto', 'galleryPhotos'));
})->name('home');

// ─────────────────────────────────────────────────
// PANDUAN PROJEK — Tampil untuk Semua Role
// ─────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/panduan/{filename?}', [PanduanController::class, 'show'])->name('panduan.show');
});

// ─────────────────────────────────────────────────
// AUTHENTICATION — Single Login SINFO
// ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LogoutController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ─────────────────────────────────────────────────
// OPERATOR — Manajemen sistem SCF
// role: operator, super_admin
// ─────────────────────────────────────────────────
Route::prefix('operator')
    ->name('operator.')
    ->middleware(['auth', 'role:operator,super_admin'])
    ->group(function () {
        Route::get('/dashboard', [OperatorDashboard::class, 'index'])->name('dashboard');
        Route::post('/system/toggle', [OperatorDashboard::class, 'toggleSystem'])->name('system.toggle');

        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::put('/assignments/{id}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

        Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::put('/documents/{id}', [DocumentController::class, 'update'])->name('documents.update');
        Route::post('/documents/{id}/publish', [DocumentController::class, 'publish'])->name('documents.publish');
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // ── Manajemen Foto Landing Page ───────────────────────────────
        Route::get('/landing-photos', [LandingPhotoController::class, 'index'])->name('landing_photos.index');
        Route::post('/landing-photos', [LandingPhotoController::class, 'store'])->name('landing_photos.store');
        Route::put('/landing-photos/{id}', [LandingPhotoController::class, 'update'])->name('landing_photos.update');
        Route::delete('/landing-photos/{id}', [LandingPhotoController::class, 'destroy'])->name('landing_photos.destroy');

        // ── Editor Markdown Panduan Projek ────────────────────────────
        Route::get('/markdown', [MarkdownGuideController::class, 'index'])->name('markdown.index');
        Route::get('/markdown/edit/{filename?}', [MarkdownGuideController::class, 'edit'])->name('markdown.edit');
        Route::post('/markdown/save', [MarkdownGuideController::class, 'save'])->name('markdown.save');
        Route::post('/markdown/create', [MarkdownGuideController::class, 'create'])->name('markdown.create');

        // ── Konfigurasi Sistem Penilaian ──────────────────────────────
        Route::get('/score-settings', [ScoreSettingController::class, 'index'])->name('score_settings.index');
        Route::post('/score-settings/weights', [ScoreSettingController::class, 'updateWeights'])->name('score_settings.weights');
        Route::post('/score-settings/contribution-factors', [ScoreSettingController::class, 'updateContributionFactors'])->name('score_settings.contribution_factors');
        Route::put('/score-settings/criteria/{id}', [ScoreSettingController::class, 'updateCriterion'])->name('score_settings.criteria.update');
    });

// ─────────────────────────────────────────────────
// GURU — Manajemen kelompok dan penilaian IPA
// role: guru, guru_unassigned
// ─────────────────────────────────────────────────
Route::prefix('guru')
    ->name('guru.')
    ->middleware(['auth', 'role:guru,guru_unassigned'])
    ->group(function () {
        Route::get('/dashboard', [GuruDashboard::class, 'index'])->name('dashboard');

        Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
        Route::post('/groups', [GroupController::class, 'store'])->name('groups.store')->middleware('system.open');
        Route::put('/groups/{id}', [GroupController::class, 'update'])->name('groups.update')->middleware('system.open');
        Route::delete('/groups/{id}', [GroupController::class, 'destroy'])->name('groups.destroy');

        // Penilaian IPA + Poster (guru)
        Route::get('/assessments', [GuruAssessment::class, 'index'])->name('assessments.index');
        Route::post('/assessments', [GuruAssessment::class, 'store'])->name('assessments.store');
        Route::post('/assessments/{id}/rollback', [GuruAssessment::class, 'rollback'])->name('assessments.rollback');
        Route::post('/posters/{posterId}/status', [GuruAssessment::class, 'updatePosterStatus'])->name('posters.status');

        // Rekap nilai kelompok — hanya guru yang buat kelompok
        Route::get('/scores/{groupId}', [GuruScore::class, 'show'])->name('scores.show');

        // Finalisasi nilai
        Route::post('/groups/{groupId}/finalize', [FinalizeController::class, 'finalize'])->name('groups.finalize');
    });

// ─────────────────────────────────────────────────
// JURI — Penilaian kelompok (makanan)
// role: juri (guru dengan assignment juri di SCF)
// ─────────────────────────────────────────────────
Route::prefix('juri')
    ->name('juri.')
    ->middleware(['auth', 'role:juri'])
    ->group(function () {
        Route::get('/dashboard', [JuriDashboard::class, 'index'])->name('dashboard');
        Route::get('/assessments', [JuriAssessment::class, 'index'])->name('assessments.index');
        Route::get('/assessments/{groupId}', [JuriAssessment::class, 'show'])->name('assessments.show');
        Route::post('/assessments/{groupId}', [JuriAssessment::class, 'store'])->name('assessments.store');
        Route::post('/assessments/{groupId}/rollback', [JuriAssessment::class, 'rollback'])->name('assessments.rollback');
    });

// ─────────────────────────────────────────────────
// SISWA — Dashboard, poster, metadata produk, kontribusi
// role: siswa
// ─────────────────────────────────────────────────
Route::prefix('siswa')
    ->name('siswa.')
    ->middleware(['auth', 'role:siswa'])
    ->group(function () {
        Route::get('/dashboard', [SiswaDashboard::class, 'index'])->name('dashboard');

        // Upload poster & Delete poster (ketua saja, saat sistem open)
        Route::post('/poster/upload', [PosterController::class, 'upload'])
            ->name('poster.upload')
            ->middleware('system.open');
        Route::delete('/poster/delete', [PosterController::class, 'destroy'])
            ->name('poster.destroy')
            ->middleware('system.open');

        // Metadata produk (ketua saja)
        Route::get('/product-metadata', [ProductMetadataController::class, 'index'])->name('product_metadata.index');
        Route::post('/product-metadata', [ProductMetadataController::class, 'store'])
            ->name('product_metadata.store')
            ->middleware('system.open');
        Route::delete('/product-metadata/delete', [ProductMetadataController::class, 'destroy'])
            ->name('product_metadata.destroy')
            ->middleware('system.open');

        // Kontribusi anggota (ketua saja)
        Route::get('/contribution', [ContributionController::class, 'index'])->name('contribution.index');
        Route::post('/contribution', [ContributionController::class, 'store'])
            ->name('contribution.store')
            ->middleware('system.open');
        Route::delete('/contribution/delete', [ContributionController::class, 'destroy'])
            ->name('contribution.destroy')
            ->middleware('system.open');
    });

// ─────────────────────────────────────────────────
// KEPALA SEKOLAH — Monitoring read-only
// role: kepsek
// ─────────────────────────────────────────────────
Route::prefix('kepsek')
    ->name('kepsek.')
    ->middleware(['auth', 'role:kepsek'])
    ->group(function () {
        Route::get('/dashboard', [KepsekController::class, 'dashboard'])->name('dashboard');
    });
