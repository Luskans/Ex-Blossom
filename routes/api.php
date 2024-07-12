<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\UserPlantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

// Route::get('/logout', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('/plants')->group(function () {
    Route::get('/', [PlantController::class, 'index'])->name('plants.index');
    Route::post('/', [PlantController::class, 'store'])->name('plants.store');
    Route::get('/update', [PlantController::class, 'update'])->name('plants.update');
    Route::get('/refresh', [PlantController::class, 'refresh'])->name('plants.refresh');
    Route::get('/{name}', [PlantController::class, 'show'])->name('plants.show');
    Route::delete('/{id}', [PlantController::class, 'destroy'])->name('plants.destroy');
});

Route::middleware('auth:sanctum')->post('/user/plants', [UserPlantController::class, 'store'])->name('user.plants.add');
Route::middleware('auth:sanctum')->delete('/user/plants/{id}', [UserPlantController::class, 'destroy'])->name('user.plants.remove');



