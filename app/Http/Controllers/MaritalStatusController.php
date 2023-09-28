<?php

namespace App\Http\Controllers;

use App\Models\MaritalStatus;
use Illuminate\Http\Request;

class MaritalStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = MaritalStatus::all();
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
            $marital_status = new MaritalStatus();
            $marital_status->name = $request->name;
            $marital_status->status = 1;
            $id = $marital_status->save();
            return response()->json([
                'success' => true,
                'message' => 'Marital status create successfully.',
                'data' => $id,
            ]);
        } catch(Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Marital status creation failed',
                "error" => $e->message,
            ]);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(MaritalStatus $marital_status)
    {
        
        return response()->json([
            'success' => true,
            'message' => 'Marital status sent successfully.',
            'data' => $marital_status,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, MaritalStatus $marital_status)
    {
        $data = $request->all();
        $marital_status->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Marital status updated successfully',
            'data' => $marital_status,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaritalStatus $marital_status)
    {
        try {
            $marital_status->delete();
            return response()->json([
                'success' => true,
                'message' => 'Marital status deleted successfully.',
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
