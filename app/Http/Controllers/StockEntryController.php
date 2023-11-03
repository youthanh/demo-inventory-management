<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockEntry;
use App\Models\Batch;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiDataProcessingTrait;

class StockEntryController extends Controller
{
    use ApiDataProcessingTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = new StockEntry;
        $query = $model->query();
        $this->applyQuery($query, $model);
        $result = $query->with('warehouse')->simplePaginate(request('per_page', null));
        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate
        $stockEntry = new StockEntry;
        $batch = new Batch;
        $manyName = 'items';
        $validate = $this->applyValidate($stockEntry->validate, ['name' => $manyName, 'validate' => $batch->validate]);
        unset($validate[$manyName.'.*.warehouse_id']);
        unset($validate[$manyName.'.*.date']);
        $request->validate($validate);

        $submitedData = $stockEntry->create($request->all());

        if (!empty($submitedData->id)) {
            $stockEntryItemsData = $request->input('items', []);
            foreach ($stockEntryItemsData as $itemData) {
                $itemData['stock_entry_id'] = $submitedData->id;
                $itemData['warehouse_id'] = $submitedData->warehouse_id;
                $itemData['date'] = $submitedData->date;
                $batch->create($itemData);
            }
            return response()->json(['message' => 'Lưu thành công', 'data' => $submitedData], 201);
        }
        return response()->json(['message' => 'Lưu thất bại'], 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = StockEntry::with('warehouse')->with('items')->find($id);
        if (!$model) {
            return response()->json(['message' => 'Phiếu không tồn tại'], 404);
        }

        return response()->json($model, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $stockEntry = StockEntry::find($id);
        if (!$stockEntry) {
            return response()->json(['message' => 'Phiếu không tồn tại'], 404);
        }

        // Validate
        $batch = new Batch;
        $validate = $this->applyValidate($stockEntry->validate, ['name' => 'items', 'validate' => $batch->validate], 'update', ['id' => $id, 'fields' => ['code']]);
        $request->validate($validate);

        $isSuccess = $stockEntry->update($request->all());
        if ($isSuccess) {
            $stockEntryItemsData = $request->input('items', []);
            if (!empty($stockEntryItemsData)) {
                $stockEntry->items()->delete();
                foreach ($stockEntryItemsData as $itemData) {
                    $itemData['stock_entry_id'] = $stockEntry->id;
                    $itemData['warehouse_id'] = $stockEntry->warehouse_id;
                    $itemData['date'] = $stockEntry->date;

                    $batch->create($itemData);
                }
            }
            return response()->json(['message' => 'Lưu thành công', 'data' => $stockEntry], 200);
        }

        return response()->json(['message' => 'Lưu thất bại'], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stockEntry = StockEntry::find($id);
        if (!$stockEntry) {
            return response()->json(['message' => 'Phiếu không tồn tại'], 404);
        }

        $stockEntry->items()->delete();
        $stockEntry->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }
}
