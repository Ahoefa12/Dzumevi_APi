<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\Transactions;
use FedaPay\FedaPay;
use FedaPay\Transaction as FedaPayTransaction;
use FedaPay\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaiementsController extends Controller
{
    public function __construct()
    {
        // Configuration FedaPay
        $apiKey = env('FEDAPAY_SECRET_KEY');
        $environment = env('FEDAPAY_ENVIRONMENT');

        if (!$apiKey) {
            Log::error('Clé API FedaPay non configurée');
        }

        FedaPay::setApiKey($apiKey);
        FedaPay::setEnvironment($environment);

        // Désactiver SSL en sandbox
        if ($environment === 'sandbox') {
            FedaPay::setVerifySslCerts(false);
        }
    }

    /**
     * Test de connexion FedaPay
     */
    public function testConnection()
    {
        try {
            // Test simple avec la liste des clients
            $customers = Customer::all(['per_page' => 1]);
            return response()->json([
                'success' => true,
                'message' => 'Connexion FedaPay OK',
                'environment' => env('FEDAPAY_ENVIRONMENT')
            ]);
        } catch (\Throwable $th) {
            Log::error('Test connexion FedaPay échoué', [
                'message' => $th->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur connexion: ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Vote anonyme → Paiement FedaPay
     */
    public function doVote(Request $request, $candidatId)
    {
        $candidat = Candidat::find($candidatId);
        if (!$candidat) {
            return response()->json(['success' => false, 'message' => 'Candidat non trouvé'], 404);
        } else {

            $validated = $request->validate([
                'name'         => 'required|string|max:100',
                'email'        => 'required|email',
                'phone_number' => 'required|regex:/^\+?[0-9]{10,15}$/',
                'country'      => 'required|string|size:2',
                'votes'        => 'required|integer|min:1',
                'amount'       => 'required|numeric|min:100',
                'currency'     => 'required|string|size:3|in:XOF,XAF,USD',
                'mode'         => 'required|string',
            ]);

            try {
                Log::info('Début processus paiement', ['candidat_id' => $candidatId]);

                // 1. Créer le client FedaPay
                $customer = Customer::create([
                    'firstname' => explode(' ', trim($validated['name']))[0] ?? 'Voteur',
                    'lastname'  => explode(' ', trim($validated['name']))[1] ?? 'Anonyme',
                    'email'     => $validated['email'],
                    'phone_number' => [
                        'number'  => $validated['phone_number'],
                        'country' => strtolower($validated['country']),
                    ],
                ]);

                Log::info('Client FedaPay créé', ['customer_id' => $customer->id]);

                // 2. Créer la transaction FedaPay
                $apiKey = env('FEDAPAY_SECRET_KEY');
                $environment = env('FEDAPAY_ENVIRONMENT');

                if (!$apiKey) {
                    Log::error('Clé API FedaPay non configurée');
                }

                FedaPay::setApiKey($apiKey);
                FedaPay::setEnvironment($environment);
                $amount = $validated['amount'];
                $fedapayTx = FedaPayTransaction::create([
                    'description'  => "Achat de vote(s)",
                    'currency'     => ['iso' => 'XOF'],
                    'amount'       => intval($amount, 10),
                    // 'amount'       => 2000,
                    'mode'         => $validated['mode'],  
                    'callback_url' => route('payment.webhook'),
                    'customer'     => ['id' => $customer->id],
                ]);

                // $fedapayTx->sendNow(
                //     $validated['mode']
                //     // , [
                //     //     'number'  => $validated['phone_number'],
                //     //     'country' => strtoupper($validated['country']),
                //     // ]
                // );

                // Log::info('Transaction FedaPay créée', ['fedapay_id' => $fedapayTx->id]);

                // 3. Sauvegarder localement 
                $transaction = Transactions::create([
                    'candidate_id'          => $candidat->id,
                    'votes'                 => $validated['votes'],
                    'amount'                => $validated['amount'],
                    'currency'              => strtoupper($validated['currency']),
                    'name'                  => $validated['name'],
                    'email'                 => $validated['email'],
                    'phone_number'          => $validated['phone_number'],
                    'country'               => strtoupper($validated['country']),
                    'status'                => 'pending',
                    'fedapay_transaction_id' => $fedapayTx->id,
                    'reference'             => 'VOTE_' . strtoupper(Str::random(10)),
                ]);

                Log::info('Transaction locale sauvegardée', ['local_id' => $transaction->id]);

                // 4. Envoyer le paiement mobile
                $token = $fedapayTx->generateToken();
                $phone_number = [
                    'number'  => $validated['phone_number'],
                    'country' => strtolower($validated['country']),
                ];

                Log::info('Envoi paiement mobile', [
                    'mode' => $validated['mode'],
                    'phone' => $validated['phone_number'],
                    'test' => $token->token,
                    'test2' => $phone_number,
                ]);
                
                
                // $fedapayTx->sendNowWithToken($validated['mode'], $token->token, $phone_number);

                $fedapayTx->sendNow($validated['mode'], $phone_number);

                Log::info('Paiement envoyé avec succès');

                // 5. Réponse JSON
                return response()->json([
                    'success'        => true,
                    'transaction_id' => $transaction->id,
                    'fedapay_tx_id'  => $fedapayTx->id,
                    'payment_url'    => "https://checkout.fedapay.com/token",
                    'token'          => $token,
                    'message'        => 'Demande de paiement envoyée. Vérifiez votre téléphone.',
                ], 201);
            } catch (\FedaPay\Error\ApiConnection $e) {
                Log::error('Erreur connexion API FedaPay', [
                    'message' => $e->getMessage(),
                    'data' => $validated
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de connexion avec le service de paiement. Veuillez réessayer.',
                    'error' => $e->getMessage()
                ], 503);
            } catch (\FedaPay\Error\InvalidRequest $e) {
                Log::error('Requête invalide FedaPay', [
                    'message' => $e->getMessage(),
                    'data' => $validated
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Données de paiement invalides.',
                    'error' => $e->getMessage()
                ], 400);
            } catch (\Throwable $th) {
                Log::error('Erreur inattendue doVote', [
                    'message' => $th->getMessage(),
                    'trace' => $th->getTraceAsString(),
                    'data' => $validated,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du paiement',
                    'error' => $th->getMessage(),
                ], 500);
            }
        }
    }

    /**
     * Webhook FedaPay → Crédite les votes automatiquement
     */
    public function handleWebhook(Request $request)
    {
        $signature = $request->header('X-FedaPay-Signature');
        $secret = env('FEDAPAY_WEBHOOK_SECRET');

        if ($secret && $signature) {
            $computed = hash_hmac('sha256', $request->getContent(), $secret);
            if (!hash_equals($computed, $signature)) {
                Log::warning('Webhook FedaPay : signature invalide');
                return response('Invalid signature', 401);
            }
        }

        $payload = $request->all();
        Log::info('Webhook FedaPay reçu', $payload);

        if (!isset($payload['event']) || $payload['event'] !== 'transaction.update') {
            return response()->json(['status' => 'ignored']);
        }

        $fedapayTx = $payload['data']['object'] ?? null;
        if (!$fedapayTx || !isset($fedapayTx['id'])) {
            return response()->json(['status' => 'no_id']);
        }

        $transaction = Transactions::where('fedapay_transaction_id', $fedapayTx['id'])->first();
        if (!$transaction) {
            return response()->json(['status' => 'not_found']);
        }

        switch ($fedapayTx['status']) {
            case 'approved':
                if ($transaction->status !== 'completed') {
                    $transaction->update([
                        'status'  => 'completed',
                        'paid_at' => now(),
                    ]);
                    $this->creditVotes($transaction);
                    Log::info("Votes crédités !", ['tx_id' => $transaction->id]);
                }
                break;
            case 'canceled':
                $transaction->update(['status' => 'canceled']);
                break;
            case 'declined':
            case 'failed':
                $transaction->update(['status' => 'failed']);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Créditer les votes sur le candidat
     */
    private function creditVotes(Transactions $transaction)
    {
        $candidate = $transaction->candidate;
        if ($candidate) {
            $candidate->increment('votes', $transaction->votes);
            Log::info('Votes crédités', [
                'candidate_id' => $candidate->id,
                'votes'        => $transaction->votes,
                'transaction'  => $transaction->id,
            ]);
        }
    }

    /**
     * Vérifier le statut d'une transaction (pour polling)
     */
    public function checkStatus($id)
    {
        try {
            $tx = Transactions::findOrFail($id);
            $fedapayTx = FedaPayTransaction::retrieve($tx->fedapay_transaction_id);

            return response()->json([
                'success' => true,
                'status'  => $fedapayTx->status,
                'transaction' => $tx,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => 'Introuvable'], 404);
        }
    }

    /**
     * Liste des transactions (admin)
     */
    public function listTransactions(Request $request)
    {
        $transactions = Transactions::with('candidate')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data'    => $transactions,
        ]);
    }
}
