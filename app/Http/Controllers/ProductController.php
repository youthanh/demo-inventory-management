<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Traits\ApiDataProcessingTrait;

class ProductController extends Controller
{
    use ApiDataProcessingTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        dd(request('method'));
        $model = new Product;
        $query = $model->query();
        $this->applyQuery($query, $model);
        $result = $query->simplePaginate(request('per_page', null));
        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'string|nullable',
            'cost_price' => 'numeric|nullable',
            'selling_price' => 'numeric|nullable',
        ]);

        // Create a new product instance
        $product = new Product([
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'cost_price' => $request->input('cost_price'),
            'selling_price' => $request->input('selling_price'),
        ]);

        // Save the new product category to the database
        $product->save();

        // Return a response, e.g., a JSON response
        return response()->json(['message' => 'Lưu thành công', 'data' => $product], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
