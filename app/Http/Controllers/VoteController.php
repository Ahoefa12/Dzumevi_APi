<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Liste des votes récupérée avec succès',
            'data' => Vote::all()
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'date' => 'required|date',
                'echeance' => 'required|date',
                'statuts' => 'required|string'
            ]);

            $vote = Vote::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Vote enregistré avec succès',
                'data' => $vote
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'enregistrement du vote',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $vote = Vote::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Vote trouvé',
                'data' => $vote
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vote introuvable',
                'error' => $th->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'firstname' => 'sometimes|string',
                'lastname' => 'sometimes|string',
                'date' => 'sometimes|date',
                'echeance' => 'sometimes|date',
                'statuts' => 'sometimes|string'
            ]);

            $vote = Vote::findOrFail($id);
            $vote->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Vote mis à jour avec succès',
                'data' => $vote
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour du vote',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $vote = Vote::findOrFail($id);
            $vote->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Vote supprimé avec succès'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression du vote',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
