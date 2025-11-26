<?php

namespace App\Http\Controllers;
use App\Models\Vote;
use Illuminate\Http\Request;
use App\Enums\VoteStatus;
use App\Http\Traits\ApiResponse;
use Illuminate\Validation\Rule;

class VoteController extends Controller
{
    use ApiResponse;
    public function index()
    {
        
       try {
            $candidates = Vote::all();
            return $this->success($candidates, 'Candidats récupérés avec succès');
        } catch (\Exception $e) {
            return $this->error('Erreur lors de la récupération des candidats', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'date' => 'required|date',
                'echeance' => 'required|date',
                'statuts' => ['required', Rule::in(VoteStatus::values())],
            ]);

            $vote = Vote::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Vote enregistré avec succès',
                'data' => $vote,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                'status' => 'error',
                'message' => 'Erreur lors de l\'enregistrement du vote',
                'error' => $th->getMessage(),
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
                'data' => $vote,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vote introuvable',
                'error' => $th->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|string',
                'date' => 'sometimes|date',
                'echeance' => 'sometimes|date',
                'statuts' => ['sometimes', Rule::in(VoteStatus::values())],
            ]);

            $vote = Vote::findOrFail($id);
            $vote->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Vote mis à jour avec succès',
                'data' => $vote,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour du vote',
                'error' => $th->getMessage(),
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
                'message' => 'Vote supprimé avec succès',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression du vote',
                'error' => $th->getMessage(),
            ], 400);
        }
    }
}
