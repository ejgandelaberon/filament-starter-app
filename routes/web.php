<?php

declare(strict_types=1);

use App\Http\Controllers\UsersController;
use App\Livewire\DemoPage;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect(Filament::getUrl()));

Route::get('datatables', DemoPage::class);
Route::get('data', UsersController::class)->name('data');
Route::post('data', UsersController::class)->name('data');
