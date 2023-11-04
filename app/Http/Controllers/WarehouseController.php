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
        $result = $query->simplePaginate(request('per_page', null));
        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $warehouse = new Warehouse;
        $request->validate($warehouse->validate);

        $submitedData = $warehouse->create($request->all());
        if (!empty($submitedData->id)) {
            return response()->json(['message' => 'Lưu thành công', 'data' => $submitedData], 201);
        }
        return response()->json(['message' => 'Lưu thất bại'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $warehouse = Warehouse::find($id);
        if (!$warehouse) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

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

        $validate = $this->applyValidate($warehouse->validate, [], 'update', ['id' => $id, 'fields' => ['code']]);

        $request->validate($validate);
        $isSuccess = $warehouse->update($request->all());

        if ($isSuccess) {
            return response()->json(['message' => 'Lưu thành công', 'data' => $warehouse]);
        }
        return response()->json(['message' => 'Lưu thất bại'], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $warehouse = Warehouse::find($id);
        if (!$warehouse) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        $warehouse->delete();

        return response()->json(['message' => 'Xóa thành công'], 200);
    }

    public function inventory(string $id) {
        $warehouse = Warehouse::find($id);
        if (!$warehouse) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }
        $result = $warehouse->inventory();
        return response()->json(['data' => $result], 200);
    }
}
