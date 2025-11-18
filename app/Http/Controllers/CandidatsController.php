<?php
namespace App\Http\Controllers;

use App\Models\Candidat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidatsController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Liste des candidats récupérée avec succès',
            'data' => Candidat::all()
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'firstname' => 'required|string',
                'matricule' => 'required|string|unique:candidats',
                'description' => 'nullable|string',
                'categorie' => 'required|string',
                'photo' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
                "vote_id" => 'required|exists:votes,id'
            ]);

            // Gérer l'upload de la photo si présente
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('candidats', 'public');
                $data['photo'] = $photoPath;
            }

            $candidat = Candidat::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Candidat enregistré avec succès',
                'data' => [
                    'id' => $candidat->id,
                    'firstname' => $candidat->firstname,
                    'matricule' => $candidat->matricule,
                    'description' => $candidat->description,
                    'categorie' => $candidat->categorie,
                    'photo_url' => $candidat->photo_url,
                    'vote_id' => $candidat->vote_id,
                ],
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
                'matricule' => 'sometimes|string|unique:candidats,matricule,' . $id,
                'description' => 'nullable|string',
                'categorie' => 'sometimes|string',
                'photo' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048'
            ]);

            $candidat = Candidat::findOrFail($id);

            // Gérer le remplacement de la photo
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($candidat->photo && Storage::disk('public')->exists($candidat->photo)) {
                    Storage::disk('public')->delete($candidat->photo);
                }
                $photoPath = $request->file('photo')->store('candidats', 'public');
                $data['photo'] = $photoPath;
            }

            $candidat->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Candidat mis à jour avec succès',
                'data' => [
                    'id' => $candidat->id,
                    'firstname' => $candidat->firstname,
                    'matricule' => $candidat->matricule,
                    'description' => $candidat->description,
                    'categorie' => $candidat->categorie,
                    'photo_url' => $candidat->photo_url,
                    'vote_id' => $candidat->vote_id,
                ]
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


    public function candidatsByConcours(string $id)
    {
        $candidats = Candidat::where('vote_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Liste des candidats récupérée avec succès',
            'data' => $candidats
        ], 200);
    }
}