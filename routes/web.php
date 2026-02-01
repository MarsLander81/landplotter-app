<?php

use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PlotterController;
use Illuminate\Support\Facades\Route;
Route::get('/', [PageController::class, 'index'])->name('welcome');
Route::get('plot-view',[PageController::class, 'plotView'])->name('plot-view');
Route::post('/lot-submit',[PlotterController::class,'plotLand'])->name('lot-submit');