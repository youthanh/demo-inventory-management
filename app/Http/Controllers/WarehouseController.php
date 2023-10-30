<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = [
            'message' => 'index',
        ];
        $statusCode = 409;
        return response()->json($response, $statusCode);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $response = [
            'message' => 'store',
            'request' => $request,
        ];
        $statusCode = 409;
        return response()->json($response, $statusCode);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $response = [
            'message' => 'show',
            'id' => $id,
        ];
        $statusCode = 409;
        return response()->json($response, $statusCode);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $response = [
            'message' => 'update',
            'request' => $request,
            'id' => $id,
        ];
        $statusCode = 409;
        return response()->json($response, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $response = [
            'message' => 'destroy',
            'id' => $id,
        ];
        $statusCode = 409;
        return response()->json($response, $statusCode);
    }
}
