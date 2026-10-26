<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OutreachEmailController;
use App\Http\Controllers\WhatsAppOutreachController;
use App\Http\Controllers\WorkspaceAuthController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceResourceController;
use Illuminate\Support\Facades\Route;

Route::domain('admin.dylenwolff.com')->group(function (): void {
    Route::get('/login', [WorkspaceAuthController::class, 'create'])->name('login');
    Route::post('/login/code', [WorkspaceAuthController::class, 'sendCode'])->middleware('throttle:10,1')->name('workspace.login.code');
    Route::post('/login/verify', [WorkspaceAuthController::class, 'verify'])->middleware('throttle:10,1')->name('workspace.login.verify');

    Route::middleware('auth')->group(function (): void {
        Route::get('/', [WorkspaceController::class, 'index'])->name('workspace.dashboard');
        Route::get('/workspace', [WorkspaceController::class, 'index'])->name('workspace.home');
        Route::post('/logout', [WorkspaceAuthController::class, 'logout'])->name('workspace.logout');
        Route::get('/manage/{section}', [WorkspaceResourceController::class, 'index'])->name('workspace.manage');
        Route::get('/manage/{section}/create', [WorkspaceResourceController::class, 'create'])->name('workspace.resources.create');
        Route::post('/manage/{section}', [WorkspaceResourceController::class, 'store'])->name('workspace.resources.store');
        Route::get('/manage/{section}/{record}', [WorkspaceResourceController::class, 'show'])->name('workspace.resources.show');
        Route::get('/manage/{section}/{record}/edit', [WorkspaceResourceController::class, 'edit'])->name('workspace.resources.edit');
        Route::put('/manage/{section}/{record}', [WorkspaceResourceController::class, 'update'])->name('workspace.resources.update');
        Route::delete('/manage/{section}/{record}', [WorkspaceResourceController::class, 'destroy'])->name('workspace.resources.destroy');
        Route::post('/outreach-emails/{outreachEmail}/send', [OutreachEmailController::class, 'send'])->name('workspace.outreach-emails.send');
        Route::post('/leads/{lead}/whatsapp', [WhatsAppOutreachController::class, 'open'])->name('workspace.leads.whatsapp');
        Route::post('/whatsapp-outreach/{whatsappOutreach}/open', [WhatsAppOutreachController::class, 'openRecord'])->name('workspace.whatsapp.open');
        Route::post('/whatsapp-outreach/{whatsappOutreach}/sent', [WhatsAppOutreachController::class, 'markSent'])->name('workspace.whatsapp.sent');
        Route::post('/whatsapp-outreach/{whatsappOutreach}/unavailable', [WhatsAppOutreachController::class, 'markUnavailable'])->name('workspace.whatsapp.unavailable');
        Route::get('/legacy/{section?}', function (?string $section = null) {
            return $section
                ? redirect()->route('workspace.manage', $section)
                : redirect()->route('workspace.dashboard');
        });
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
