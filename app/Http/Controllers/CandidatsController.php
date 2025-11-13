<?php
namespace App\Http\Controllers;

use App\Models\Candidat;
use Illuminate\Http\Request;

class CandidatsController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Liste des candidats récupérée avec succès',
            'data' => Candidat::all()
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'firstname' => 'required|string',
                'maticule' => 'required|string|unique:candidats',
                'description' => 'nullable|string',
                'categorie' => 'required|string',
                'photo' => 'nullable|string' // ou 'image' si tu gères l'upload
            ]);

            $candidat = Candidat::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Candidat enregistré avec succès',
                'data' => $candidat,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du candidat',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $candidat = Candidat::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Candidat trouvé',
                'data' => $candidat
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Candidat introuvable',
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
                'maticule' => 'sometimes|string|unique:candidats,maticule,' . $id,
                'description' => 'nullable|string',
                'categorie' => 'sometimes|string',
                'photo' => 'nullable|string'
            ]);

            $candidat = Candidat::findOrFail($id);
            $candidat->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Candidat mis à jour avec succès',
                'data' => $candidat
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour du candidat',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $candidat = Candidat::findOrFail($id);
            $candidat->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Candidat supprimé avec succès'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression du candidat',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
