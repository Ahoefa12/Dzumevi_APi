<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatsController;
use App\Http\Controllers\ConcoursController;
use App\Http\Controllers\PaiementsController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;
// Route::get('/test', function() {
//     return response()->json(['message' => 'API fonctionne !']);
// });


// Authentification
Route::post('/login', [AuthController::class, 'login']);


route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('candidats', CandidatsController::class)->except(['index', 'show']);
    Route::apiResource('votes', VoteController::class)->except(['index', 'show']);
    Route::get('/paiements/list', [PaiementsController::class, 'listTransactions']);
});

// Routes publiques (vote anonyme ou ancien système)
Route::post('/payment/{candidatId}/vote', [PaiementsController::class, 'doVote'])
    ->name('payment.dovote');

// Routes authentifiées (nouveau système recommandé)

// Vérifier le statut d'une transaction
Route::get('/payment/status/{transaction}', [PaiementsController::class, 'checkStatus']);

// Lister ses propres transactions (ou toutes si admin)
Route::get('/payment/my-transactions', [PaiementsController::class, 'myTransactions']); // à ajouter si besoin


// Admin ou dashboard
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/paiements/list', [PaiementsController::class, 'listTransactions'])
        ->name('paiements.list');
});

Route::post('/paiements/{candidatId}/vote', [PaiementsController::class, 'doVote']);

// Webhook FedaPay (doit être public et sans auth)
Route::post('/payment/webhook', [PaiementsController::class, 'handleWebhook'])
    ->name('payment.webhook');
Route::get('candidats', [CandidatsController::class, 'index']);
Route::get('candidats/{id}', [CandidatsController::class, 'show']);
// Route::get('concours', [VoteController::class, 'index']);
// Route::get('concours/{id}', [VoteController::class, 'show']);
// Route::get('concours/{id}/candidats', [CandidatsController::class, 'candidatsByConcours']);


Route::prefix('concours')->group(function () {
    Route::get('/', [ConcoursController::class, 'index']);
    Route::get('/actifs', [ConcoursController::class, 'actifs']);
    Route::get('/{concours}', [ConcoursController::class, 'show']);
    Route::get('/{concours}/candidats', [ConcoursController::class, 'candidats']);
    Route::patch('/{concours}/stats', [ConcoursController::class, 'updateStats']);
});

Route::apiResource('concours', ConcoursController::class);
Route::get('concours/{concours}/candidats', [ConcoursController::class, 'candidats']);
Route::post('concours/{concours}/update-stats', [ConcoursController::class, 'updateStats']);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

use App\Http\Controllers\PaygateController;

Route::post('/paygate/initiate', [PaygateController::class, 'initiatePayment']);
Route::post('/paygate/webhook', [PaygateController::class, 'webhook'])->name('paygate.webhook');
Route::post('/paygate/status', [PaygateController::class, 'checkStatus']);
Route::get('/paygate/balance', [PaygateController::class, 'checkBalance']);