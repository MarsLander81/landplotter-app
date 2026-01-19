<?php

use App\Http\Controllers\DatabaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/location',[DatabaseController::class,'fetchLocation']);
Route::get('/citymun',[DatabaseController::class,'fetchCityMun']);
Route::get('/pointreference',[DatabaseController::class,'fetchPointReference']);