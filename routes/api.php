<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatsController;
use App\Http\Controllers\PaiementsController;
use App\Http\Controllers\VoteController;

Route::apiResource('candidats', CandidatsController::class);
Route::apiResource('paiements', PaiementsController::class);
Route::apiResource('votes', VoteController::class);



// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

