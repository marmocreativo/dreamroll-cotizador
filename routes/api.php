<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\PortafolioApiController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('portafolio', [PortafolioApiController::class, 'index']);
Route::get('portafolio/{evento}', [PortafolioApiController::class, 'show']);
