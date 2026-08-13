<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BuilderOnboardingController;
use App\Http\Controllers\PageBuilderController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

RateLimiter::for('builder-save', fn (Request $request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->user()?->id ?? $request->ip()));

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('builder/onboarding', [BuilderOnboardingController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('builder.onboarding');

Route::post('builder/onboarding', [BuilderOnboardingController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('builder.onboarding.update');

Route::post('builder/onboarding/revisions/publish', [BuilderOnboardingController::class, 'publishRevision'])
    ->middleware(['auth', 'verified'])
    ->name('builder.onboarding.revisions.publish');

Route::post('builder/onboarding/revisions/rollback', [BuilderOnboardingController::class, 'rollbackRevision'])
    ->middleware(['auth', 'verified'])
    ->name('builder.onboarding.revisions.rollback');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// Page Builder (legacy page designs)
Route::middleware(['auth', 'verified'])->prefix('builder/designs')->name('builder.designs.')->group(function () {
    Route::get('/',                  [PageBuilderController::class, 'index'])->name('index');
    Route::get('/create',            [PageBuilderController::class, 'create'])->name('create');
    Route::get('/{design}/edit',     [PageBuilderController::class, 'edit'])->name('edit');
    Route::get('/{design}/data',     [PageBuilderController::class, 'data'])->name('data');
    Route::post('/save',             [PageBuilderController::class, 'save'])->middleware('throttle:builder-save')->name('save');
    Route::post('/{design}/publish', [PageBuilderController::class, 'publish'])->name('publish');
    Route::delete('/{design}',       [PageBuilderController::class, 'destroy'])->name('destroy');
});

// Projects
Route::middleware(['auth', 'verified'])->prefix('projects')->name('projects.')->group(function () {
    Route::get('/',                   [ProjectController::class, 'index'])->name('index');
    Route::get('/create',             [ProjectController::class, 'create'])->name('create');
    Route::post('/',                  [ProjectController::class, 'store'])->name('store');
    Route::post('/save',              [ProjectController::class, 'save'])->middleware('throttle:builder-save')->name('save');
    Route::get('/{project}',          [ProjectController::class, 'show'])->name('show');
    Route::get('/{project}/canvas',   [ProjectController::class, 'canvas'])->name('canvas');
    Route::get('/{project}/data',     [ProjectController::class, 'data'])->name('data');
    Route::post('/{project}/publish', [ProjectController::class, 'publish'])->name('publish');
    Route::delete('/{project}',       [ProjectController::class, 'destroy'])->name('destroy');
});

// Extensions marketplace
Route::middleware(['auth', 'verified'])
    ->get('/extensions', function () {
        return view('extensions.index');
    })
    ->name('extensions.index');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

// Admin (requires is_admin gate)
Route::middleware(['auth', 'verified', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/users/{user}/module-generation/retry', [AdminController::class, 'retryModuleGeneration'])
        ->name('module-generation.retry');
});

require __DIR__.'/auth.php';
