<?php

namespace App\Http\Controllers;

use App\Enums\VoteStatus;
use App\Models\Concours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ConcoursController extends Controller
{

    public function store(Request $request)
    {
        try {
            // Validation des données
            $validator = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'date_debut' => 'required|date|after_or_equal:today',
                'date_fin' => 'required|date|after:date_debut',
                'statut' => 'sometimes|string|in:' . implode(',', VoteStatus::values()),
                // 'image_url' => 'sometimes|mimes:jpg,png,jpeg,gif,svg|max:2048',
                'prix_par_vote' => 'required|numeric|min:0',
                'is_active' => 'sometimes',
            ]);

            // if ($validator->fails()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Erreur de validation',
            //         'errors' => $validator->errors()
            //     ], 422);
            // }

            // Traitement de l'image
            $imagePath = null;
            if ($request->hasFile('image_url')) {
                $imagePath = $request->file('image_url')->store('concours', 'public');
            }

            // Création du concours
            $concours = Concours::create([
                'name' => $request->name,
                'description' => $request->description,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'statut' => $request->statut,
                'image_url' => $imagePath,
                'prix_par_vote' => $request->prix_par_vote,
                'nombre_candidats' => 0,
                'nombre_votes' => 0,
                'total_recettes' => 0,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Concours créé avec succès',
                'data' => $concours
            ], 201);

        } catch (\Exception $e) {
            // Supprimer l'image en cas d'erreur
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du concours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Concours $concours)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'date_debut' => 'sometimes|date',
                'date_fin' => 'sometimes|date|after:date_debut',
                'statut' => 'sometimes|string|in:actif,inactif,termine',
                'image_url' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'prix_par_vote' => 'sometimes|numeric|min:0',
                'is_active' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Traitement de la nouvelle image
            if ($request->hasFile('image_url')) {
                // Supprimer l'ancienne image si elle existe
                if ($concours->image_url && Storage::disk('public')->exists($concours->image_url)) {
                    Storage::disk('public')->delete($concours->image_url);
                }
                
                $imagePath = $request->file('image_url')->store('concours', 'public');
                $request->merge(['image_url' => $imagePath]);
            }

            $concours->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Concours mis à jour avec succès',
                'data' => $concours->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du concours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Concours $concours)
    {
        try {
            // Vérifier s'il y a des candidats associés
            if ($concours->candidats()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer le concours car il contient des candidats'
                ], 422);
            }

            // Supprimer l'image associée
            if ($concours->image_url && Storage::disk('public')->exists($concours->image_url)) {
                Storage::disk('public')->delete($concours->image_url);
            }

            $concours->delete();

            return response()->json([
                'success' => true,
                'message' => 'Concours supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du concours',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
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
    public function show(Concours $concours)
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
    public function actifs()
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
    public function candidats(Concours $concours)
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
    public function updateStats(Concours $concours)
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
