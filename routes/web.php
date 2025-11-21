<!-- <?php

use App\Http\Controllers\CandidatsController;
use App\Http\Controllers\PaiementsController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::Post('/paiement', [PaiementsController::class, 'doVote']);
Route::get('/candidats', [CandidatsController::class, 'index']);
Route::get('/candidats/{id}', [CandidatsController::class, 'show']);

Route::get('/votes', [VoteController::class, 'index']);
Route::get('/votes/{id}', [VoteController::class, 'show']);

