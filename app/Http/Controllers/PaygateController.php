<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaygateController extends Controller
{
    // Configuration (à mettre dans .env plus tard)
    private $api_key;
    private $base_url = 'https://paygateglobal.com';

    public function __construct()
    {
        $this->api_key = '2647da99-e166-4ada-95f6-154f67168630'; // À ajouter dans ton .env
    }

    /**
     * Initier un paiement (Méthode 1 - API directe)
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|regex:/^228[0-9]{8}$/', // Togo uniquement pour l'exemple
            'amount'       => 'required|numeric|min:100',
            'network'      => 'required|in:FLOOZ,TMONEY',
            'description'  => 'nullable|string|max:255',
            'order_id'     => 'required|string', // ou ta table
        ]);

        $identifier = 'ORD_' . date('Ymd') . '_' . Str::upper(Str::random(8));

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->base_url}/api/v1/pay", [
            'auth_token'    => $this->api_key,
            'phone_number'  => $request->phone_number,
            'amount'        => (int) $request->amount,
            'network'       => $request->network,
            'identifier'    => $identifier,
            'description'   => $request->description ?? 'Paiement via ' . $request->network,
        ]);

        if ($response->failed()) {
            Log::error('PayGate initiation failed', $response->json());
            return response()->json(['error' => 'Service indisponible'], 500);
        }

        $data = $response->json();

        if ($data['status'] != 0) {
            $errors = [
                2 => 'Clé API invalide',
                4 => 'Paramètres invalides',
                6 => 'Transaction déjà existante (doublon)',
            ];
            return response()->json([
                'error' => $errors[$data['status']] ?? 'Erreur inconnue',
                'status' => $data['status']
            ], 400);
        }

        // Sauvegarder la transaction en attente
        Payment::create([
            'order_id'         => $request->order_id,
            'identifier'       => $identifier,
            'tx_reference'     => $data['tx_reference'],
            'phone_number'     => $request->phone_number,
            'amount'           => $request->amount,
            'network'          => $request->network,
            'status'           => 'pending', // ou 2 = en cours
        ]);

        return response()->json([
            'success'       => true,
            'message'       => 'Paiement initié. Le client reçoit le prompt USSD.',
            'tx_reference'  => $data['tx_reference'],
            'identifier'    => $identifier,
        ]);
    }

    /**
     * Webhook - Réception de la confirmation de paiement
     * Route : POST /paygate/webhook (à déclarer comme publique, sans CSRF)
     */
    public function webhook(Request $request)
    {
        $payload = $request->json()->all();

        Log::info('PayGate Webhook reçu', $payload);

        // Sécurité basique : tu peux ajouter une vérification IP ou un secret partagé si PayGate le permet
        $identifier = $payload['identifier'] ?? null;
        $tx_reference = $payload['tx_reference'] ?? null;

        if (!$identifier || !$tx_reference) {
            Log::warning('Webhook PayGate : données manquantes');
            return response('Invalid data', 400);
        }

        $payment = \App\Models\Payment::where('identifier', $identifier)
                     ->orWhere('tx_reference', $tx_reference)
                     ->first();

        if (!$payment) {
            Log::warning("Webhook PayGate : transaction inconnue {$identifier}");
            return response('Unknown transaction', 404);
        }

        // Éviter les doublons
        if ($payment->status === 'success') {
            return response('OK already processed', 200);
        }

        $payment->update([
            'tx_reference'     => $payload['tx_reference'] ?? $payment->tx_reference,
            'payment_reference'=> $payload['payment_reference'] ?? null,
            'amount_paid'      => $payload['amount'],
            'phone_number'     => $payload['phone_number'],
            'payment_method'   => $payload['payment_method'],
            'paid_at'          => now(),
            'status'           => 'success',
        ]);

        // LIVRER LE SERVICE ICI (activer compte, envoyer crédit, etc.)
        // Exemple :
        $this->deliverOrder($payment->order_id);

        return response('OK', 200);
    }

    /**
     * Vérifier l'état d'une transaction (polling si besoin)
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'identifier'   => 'required|string', // ton identifier ou tx_reference
        ]);

        $payment = \App\Models\Payment::where('identifier', $request->identifier)
                     ->orWhere('tx_reference', $request->identifier)
                     ->firstOrFail();

        $url = $payment->tx_reference 
            ? "{$this->base_url}/api/v1/status"
            : "{$this->base_url}/api/v2/status";

        $body = $payment->tx_reference 
            ? ['auth_token' => $this->api_key, 'tx_reference' => $payment->tx_reference]
            : ['auth_token' => $this->api_key, 'identifier' => $payment->identifier];

        $response = Http::post($url, $body);

        if ($response->successful()) {
            $data = $response->json();
            $statusMap = [0 => 'success', 2 => 'pending', 4 => 'expired', 6 => 'cancelled'];
            $status = $statusMap[$data['status']] ?? 'unknown';

            if ($payment->status !== $status) {
                $payment->update(['status' => $status]);
                if ($status === 'success') $this->deliverOrder($payment->order_id);
            }

            return response()->json([
                'status' => $status,
                'data'   => $data
            ]);
        }

        return response()->json(['error' => 'Impossible de vérifier'], 500);
    }

    /**
     * Consulter le solde Flooz & TMoney
     */
    public function checkBalance()
    {
        $response = Http::post("{$this->base_url}/api/v1/check-balance", [
            'auth_token' => $this->api_key
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Impossible de récupérer le solde'], 500);
    }

    // Méthode privée pour livrer la commande
    private function deliverOrder($orderId)
    {
        // Ton code pour activer le service, envoyer un email, etc.
        Log::info("Livraison de la commande {$orderId} effectuée !");
        // Ex: Order::where('id', $orderId)->update(['status' => 'paid']);
    }
}