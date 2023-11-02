<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Traits\ApiDataProcessingTrait;

class WarehouseController extends Controller
{
    use ApiDataProcessingTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = new Warehouse;
        $query = $model->query();
        $this->applyQuery($query, $model);
        $result = $query->paginate(request('per_page', null));
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
            'address' => 'string|nullable',
        ]);

        // Create a new product instance
        $warehouse = new Warehouse([
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'address' => $request->input('description'),
        ]);

        // Save the new product category to the database
        $warehouse->save();

        // Return a response, e.g., a JSON response
        return response()->json(['message' => 'Lưu thành công', 'data' => $warehouse], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Tìm sản phẩm dựa trên ID
        $warehouse = Warehouse::find($id);

        // Kiểm tra xem sản phẩm có tồn tại không
        if (!$warehouse) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        // Trả về thông tin chi tiết của sản phẩm
        return response()->json($warehouse, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $warehouse = Warehouse::find($id);

        if (!$warehouse) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'address' => 'string|nullable',
        ]);
        $warehouse->update($request->all());

        return response()->json(['message' => 'Lưu thành công', 'data' => $warehouse]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Tìm sản phẩm dựa trên ID
        $warehouse = Warehouse::find($id);

        // Kiểm tra xem sản phẩm có tồn tại không
        if (!$warehouse) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        // Xóa sản phẩm
        $warehouse->delete();

        // Trả về thông báo xóa thành công
        return response()->json(['message' => 'Xóa thành công'], 200);
    }
}
