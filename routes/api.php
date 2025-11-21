<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatsController;
use App\Http\Controllers\PaiementsController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\AuthController;

// Route::get('/test', function() {
//     return response()->json(['message' => 'API fonctionne !']);
// });


// Authentification
Route::post('/login', [AuthController::class, 'login']);


route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('candidats', CandidatsController::class)->except(['index', 'show']);
    Route::apiResource('votes', VoteController::class)->except(['index', 'show']);
});



Route::get('/paiements/list', [PaiementsController::class, 'listTransactions']);
Route::post('/paiements/{candidatId}', [PaiementsController::class, 'doVote']);
Route::get('/paiements/status/{transactionId}', [PaiementsController::class, 'checkTransaction']);
Route::get('candidats', [CandidatsController::class, 'index']);
Route::get('candidats/{id}', [CandidatsController::class, 'show']);
Route::get('concours', [VoteController::class, 'index']);
Route::get('concours/{id}', [VoteController::class, 'show']);
Route::get('concours/{id}/candidats', [CandidatsController::class, 'candidatsByConcours']);


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
