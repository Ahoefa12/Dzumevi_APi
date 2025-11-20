<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {

            $validated = $request->validate([
                'name' => 'required|string|max:80',
                'password' => 'required|string|min:6',
            ]);

            $admin = Admin::where('name', $validated['name'])->first();

            if ($admin && Hash::check($validated['password'], $admin->password)) {
                $token = $admin->createToken($admin->id)->plainTextToken;

                // masquer le mot de passe avant de retourner l'utilisateur
                $admin->makeHidden(['password']);

                return response()->json([
                    'success' => true,
                    'message' => 'Authentification réussie',
                    'admin' => $admin->name,
                    'token' => $token,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Nom d\'utilisateur ou mot de passe incorrect'
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'authentification',
                'error' => $th->getMessage()
            ], 500);
        }
    }


}
