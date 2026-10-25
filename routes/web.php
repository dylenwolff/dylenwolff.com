<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceAuthController;
use Illuminate\Support\Facades\Route;

Route::domain('admin.dylenwolff.com')->group(function (): void {
    Route::get('/login', [WorkspaceAuthController::class, 'create'])->name('login');
    Route::post('/login/code', [WorkspaceAuthController::class, 'sendCode'])->middleware('throttle:10,1')->name('workspace.login.code');
    Route::post('/login/verify', [WorkspaceAuthController::class, 'verify'])->middleware('throttle:10,1')->name('workspace.login.verify');

    Route::middleware('auth')->group(function (): void {
        Route::get('/', [WorkspaceController::class, 'index'])->name('workspace.dashboard');
        Route::get('/workspace', [WorkspaceController::class, 'index'])->name('workspace.home');
        Route::get('/manage/{section}', function (string $section) {
            $allowed = [
                'organizations', 'contacts', 'leads', 'opportunities', 'crm-tasks', 'activities',
                'client-projects', 'support-tickets', 'client-documents', 'quotes', 'invoices',
                'maintenance-plans', 'projects', 'services', 'awards', 'qualifications', 'site-settings',
            ];

            abort_unless(in_array($section, $allowed, true), 404);

            return redirect('/legacy/'.$section);
        })->name('workspace.manage');
    });
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/work', [HomeController::class, 'work'])->name('work');
Route::get('/work/{project}', [HomeController::class, 'project'])->name('work.show');
Route::get('/contact', [HomeController::class, 'contactPage'])->name('contact');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::post('/contact', [HomeController::class, 'contact'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/sitemap.xml', [HomeController::class, 'sitemap'])->name('sitemap');
