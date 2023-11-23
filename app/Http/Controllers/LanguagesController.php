<?php

namespace App\Http\Controllers;

use App\Models\Languages;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Languages::all();
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $language = new Languages();
            $language->name = $request->name;
            $language->code = $request->code;
            $language->status = 1;
            $id = $language->save();
            return response()->json([
                'success' => true,
                'message' => 'Language create successfully.',
                'data' => $id,
            ]);
        } catch(Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Language creation failed',
                "error" => $e->message,
            ]);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Languages $language)
    {
        return response()->json([
            'success' => true,
            'message' => 'language sent successfully.',
            'data' => $language,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Languages $language)
    {
        $data = $request->all();
        $language->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Language updated successfully',
            'data' => $language,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Languages $language)
    {
        try {
            $language->delete();
            return response()->json([
                'success' => true,
                'message' => 'Language deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong...',
                "error" => $e->message,
            ]);
        }
    }
}
