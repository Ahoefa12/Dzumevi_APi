<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class PaiementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
               
            ]);
            return response()->json([
                
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
           
            return response()->json([
                
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         try {
            $data = $request->validate([
               
            ]);
           
            return response()->json([
                
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
           
            return response()->json([
                
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                
            ], 400);
        }
    }
}
