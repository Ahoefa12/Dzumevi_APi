<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatsController;
use App\Http\Controllers\PaiementsController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\AuthController;

// Gérer toutes les requêtes OPTIONS
Route::options('/{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('any', '.*');

// Middleware pour ajouter les headers CORS à toutes les routes API
Route::middleware(function ($request, $next) {
    $response = $next($request);
    $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept');
    $response->headers->set('Access-Control-Allow-Credentials', 'true');
    return $response;
})->group(function () {    
    
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
        
});