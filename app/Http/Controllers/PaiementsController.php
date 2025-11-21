<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\User;
use FedaPay\Customer;
use FedaPay\FedaPay;
use FedaPay\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaiementsController extends Controller
{
    public function doVote(Request $request, $candidatId)
    {
        try {
            // Valider que le candidat existe
            $candidat = Candidat::where('id', $candidatId)->first();
            if (!$candidat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Candidat non trouvé'
                ], 404);
            }

            // Validation des données de la requête
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email',
                'phone_number' => 'required|regex:/^\+?[0-9]{10,15}$/',
                'country' => 'required|string|size:2',
                'amount' => 'required|numeric|min:100',
                'currency' => 'required|string|size:3',
                'description' => 'required|string|max:255',
                'mode' => 'required|string'
            ]);

            /* Configuration FedaPay */
            FedaPay::setApiKey(env('FEDAPAY_SECRET_KEY')); // ← CLÉ DANS .ENV
            FedaPay::setEnvironment(env('FEDAPAY_ENVIRONMENT', 'live'));

            /* Créer un client */
            $customer = Customer::create([
                "firstname" => $validated['name'],
                "lastname" => 'Vote',
                "email" => $validated['email'],
                "phone_number" => [
                    "number" => $validated['phone_number'],
                    "country" => $validated['country']
                ]
            ]);

            /* Créer une transaction */
            $transaction = Transaction::create([
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'currency' => ['iso' => $validated['currency']],
                'callback_url' => config('app.callback_url', 'https://example.com/callback'),
                'mode' => $validated['mode'],
                'customer' => ['id' => $customer->id]
            ]);

            /* Enregistrer dans la table users */
            $user = User::create([
                "candidat_id" => $candidatId,
                "name" => $validated['name'],
                "email" => $validated['email'],
                "phone_number" => $validated['phone_number'],
                "country" => $validated['country'],
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'callback_url' => config('app.callback_url', 'https://example.com/callback'),
                'mode' => $validated['mode'],
                'customer' => json_encode([ // Ajout du champ customer manquant
                    'firstname' => $validated['name'],
                    'lastname' => 'Vote',
                    'email' => $validated['email'],
                    'phone_number' => [
                        'number' => $validated['phone_number'],
                        'country' => $validated['country']
                    ]
                ]),
                'transaction_id' => $transaction->id
            ]);

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
                'customer_id' => $customer->id,
                'user_id' => $user->id,
                'payment_url' => $transaction->generateToken(),
                'message' => 'Paiement initié avec succès'
            ], 201);
        } catch (\Throwable $th) {
            Log::error('Erreur paiement: ' . $th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la transaction',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    // Méthode pour vérifier le statut d'une transaction
    public function checkTransaction($transactionId)
    {
        try {
            FedaPay::setApiKey(env('FEDAPAY_SECRET_KEY')); // ← CLÉ DANS .ENV
            FedaPay::setEnvironment(env('FEDAPAY_ENVIRONMENT', 'live'));

            $transaction = Transaction::retrieve($transactionId);

            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'data' => $transaction
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function listTransactions(Request $request)
    {
        try {
            // FedaPay::setApiKey(env('FEDAPAY_SECRET_KEY')); // ← CLÉ DANS .ENV
            // FedaPay::setEnvironment(env('FEDAPAY_ENVIRONMENT', 'live'));

            // $filters = [];

            // // Filtre par statut si fourni
            // if ($request->has('status')) {
            //     $filters['status'] = $request->get('status');
            // }

            // // Filtre par date si fourni
            // if ($request->has('start_date')) {
            //     $filters['start_date'] = $request->get('start_date');
            // }

            // if ($request->has('end_date')) {
            //     $filters['end_date'] = $request->get('end_date');
            // }

            // $transactions = Transaction::search($filters);
            $transactions = User::all();

            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
