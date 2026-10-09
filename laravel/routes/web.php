<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Imperial\ImperialApiController as Api;
use App\Http\Controllers\Imperial\ImperialController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SimulatorController;
use App\Http\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

/* ------------------------------ public site ------------------------------ */
Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);
Route::get('/robots.txt', [SeoController::class, 'robots']);

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/password/request', [AuthController::class, 'requestReset']);
Route::post('/password/reset', [AuthController::class, 'reset'])->middleware('throttle:10,1');

Route::post('/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:20,1');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:10,1');
Route::post('/simulator/attempts', [SimulatorController::class, 'store'])->middleware('throttle:30,1');

/* ------------------------------ student portal ------------------------------ */
Route::get('/student', StudentPortalController::class)->middleware('role:STUDENT')->name('student.portal');

/* ------------------------- imperial control center ------------------------- */
Route::middleware('role:STAFF')->prefix('imperial')->group(function () {
    Route::prefix('api')->group(function () {
        Route::post('records/{dataset}', [Api::class, 'store']);
        Route::patch('records/{dataset}/{key}', [Api::class, 'update']);
        Route::post('records/{dataset}/{key}/transition', [Api::class, 'transition']);
        Route::delete('records/{dataset}/{key}', [Api::class, 'destroy']);
        Route::post('records/{dataset}/bulk', [Api::class, 'bulk']);
        Route::post('records/{dataset}/import', [Api::class, 'import']);
        Route::get('records/{dataset}/export', [Api::class, 'export']);

        Route::post('approvals/{key}/decide', [Api::class, 'decide']);
        Route::post('rbac/toggle', [Api::class, 'rbacToggle']);
        Route::post('rbac/reset', [Api::class, 'rbacReset']);
        Route::post('cms', [Api::class, 'cms']);
        Route::post('marketing', [Api::class, 'marketing']);
        Route::post('seo', [Api::class, 'seo']);
        Route::post('settings', [Api::class, 'settings']);
        Route::post('workspace/reset', [Api::class, 'resetWorkspace']);
        Route::post('media', [Api::class, 'mediaStore']);
        Route::post('media/{key}', [Api::class, 'mediaUpdate']);
        Route::post('note', [Api::class, 'note']);
        Route::post('denied', [Api::class, 'denied']);
    });

    Route::get('/{module?}', [ImperialController::class, 'show'])->name('imperial.show');
});
