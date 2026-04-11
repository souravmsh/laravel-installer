<?php

use Illuminate\Support\Facades\Route;
use Souravmsh\Installer\Http\Controllers\InstallerController;

Route::prefix('install')->middleware(['web', 'installer.redirect'])->name('installer.')->group(function () {
    Route::get('/', [InstallerController::class, 'welcome'])->name('welcome');
    Route::get('/database', [InstallerController::class, 'database'])->name('database');
    Route::post('/database/test', [InstallerController::class, 'testDatabase'])->name('database.test');
    Route::post('/database/create', [InstallerController::class, 'createDatabase'])->name('database.create');
    Route::post('/database', [InstallerController::class, 'saveDatabase'])->name('database.save');
    Route::get('/license', [InstallerController::class, 'license'])->name('license');
    Route::post('/license', [InstallerController::class, 'saveLicense'])->name('license.save');
    Route::get('/install', [InstallerController::class, 'install'])->name('install');
    Route::post('/install/process', [InstallerController::class, 'processInstall'])->name('install.process');
    Route::get('/complete', [InstallerController::class, 'complete'])->name('complete');
});
