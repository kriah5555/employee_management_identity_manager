<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use Illuminate\Http\Request;

class GenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Gender::all();
        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $gender = new Gender();
        $gender->name = $request->name;
        $gender->status = 1;
        $id = $gender->save();
        return response()->json([
            'success' => true,
            'message' => 'Gender create successfully.',
            'data'    => $id,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Gender $gender)
    {
        return response()->json([
            'success' => true,
            'message' => 'Gender sent successfully.',
            'data'    => $gender,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Gender $gender)
    {
        $data = $request->all();
        $gender->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Gender updated successfully',
            'data'    => $gender,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gender $gender)
    {
        $gender->delete();
        return response()->json([
            'success' => true,
            'message' => 'Gender deleted successfully.',
        ]);
    }
}