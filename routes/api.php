<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatsController;
use App\Http\Controllers\PaiementsController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\AuthController;


// Authentification
Route::post('/login', [AuthController::class, 'login']);


route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('candidats', CandidatsController::class)->except(['index', 'show']);
    Route::apiResource('votes', VoteController::class)->except(['index', 'show']);
});


Route::Post('paiement', [PaiementsController::class, 'doVote']);
Route::get('candidats', [CandidatsController::class, 'index']);
Route::get('candidats/{id}', [CandidatsController::class, 'show']);
Route::get('votes', [VoteController::class, 'index']);
Route::get('votes/{id}', [VoteController::class, 'show']);
Route::get('votes/{id}/candidats', [CandidatsController::class, 'candidatsByVote']);


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
