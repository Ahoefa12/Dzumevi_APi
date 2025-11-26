<?php

namespace App\Http\Controllers;

use App\Models\Concours;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConcoursController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $concours = Concours::where('is_active', true)
                ->orderBy('date_debut', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des concours récupérée avec succès',
                'data' => $concours
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des concours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Concours $concours): JsonResponse
    {
        try {
            // Charger les candidats avec le concours
            $concours->load('candidats');

            return response()->json([
                'success' => true,
                'message' => 'Détails du concours récupérés avec succès',
                'data' => $concours
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du concours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les concours actifs
     */
    public function actifs(): JsonResponse
    {
        try {
            $concoursActifs = Concours::actifs()
                ->orderBy('date_fin', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Concours actifs récupérés avec succès',
                'data' => $concoursActifs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des concours actifs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les candidats d'un concours
     */
    public function candidats(Concours $concours): JsonResponse
    {
        try {
            $candidats = $concours->candidats()
                ->orderBy('votes', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Candidats du concours récupérés avec succès',
                'data' => $candidats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des candidats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour les statistiques d'un concours
     */
    public function updateStats(Concours $concours): JsonResponse
    {
        try {
            $concours->updateStats();

            return response()->json([
                'success' => true,
                'message' => 'Statistiques mises à jour avec succès',
                'data' => $concours->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
