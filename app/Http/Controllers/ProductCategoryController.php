<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Traits\ApiDataProcessingTrait;

class ProductCategoryController extends Controller
{
    use ApiDataProcessingTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = new ProductCategory;
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
            'parent_category_id' => 'int|nullable',
        ]);

        // Create a new ProductCategory instance
        $productCategory = new ProductCategory([
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'parent_category_id' => $request->input('parent_category_id'),
        ]);

        // Save the new product category to the database
        $productCategory->save();

        // Return a response, e.g., a JSON response
        return response()->json(['message' => 'Lưu thành công', 'data' => $productCategory], 201);
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
