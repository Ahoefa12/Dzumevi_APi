<?php

namespace App\Http\Controllers;

use FedaPay\Customer;
use FedaPay\FedaPay;
use FedaPay\Transaction;
use Illuminate\Http\Request;


class PaiementsController extends Controller
{

    public function doVote(Request $request)
    {
        // Validation des données de la requête
        $validated = $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email',
            'phone_number' => 'required|regex:/^\+?[0-9]{10,15}$/',
            'country' => 'required|string|size:2',
            'amount' => 'required|numeric|min:100',
            'currency' => 'required|string|size:3',
            'description' => 'required|string|max:255',
            'mode' => 'required|in:mtn_open,mtn_express,moov,airtel'
        ]);

        /* Remplacez YOUR_SECRETE_API_KEY par votre clé API secrète */
        FedaPay::setApiKey("pk_live_5OlVf7A_l9Hnz1IXPULt3QWZ");
        /* Indiquez si vous souhaitez exécuter votre requête en mode test ou en live */
        FedaPay::setEnvironment('sandbox'); //or setEnvironment('live');
        
        /* Créer un client */
        $customer = Customer::create([
            "firstname" => $validated['firstname'],
            "lastname" => $validated['lastname'],
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

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'message' => 'Paiement initié avec succès'
        ], 201);
    }
}
