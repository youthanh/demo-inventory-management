<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockExit;
use App\Models\Batch;
use App\Traits\ApiDataProcessingTrait;
use App\Services\InventoryService;

class StockExitController extends Controller
{
    use ApiDataProcessingTrait;
    protected $inventoryService;
    /**
     * Display a listing of the resource.
     */
    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $model = new StockExit;
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
        $stockExit = new StockExit;
        $batch = new Batch;
        $manyName = 'items';
        $validate = $this->applyValidate($stockExit->validate, ['name' => $manyName, 'validate' => $batch->validate]);
        unset($validate[$manyName.'.*.warehouse_id']);
        unset($validate[$manyName.'.*.date']);
        $request->validate($validate);

        $submitedData = $stockExit->create($request->all());

        if (!empty($submitedData->id)) {
            $stockExitItemsData = $request->input('items', []);
            foreach ($stockExitItemsData as $itemData) {
                $itemData['stock_exit_id'] = $submitedData->id;
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
        $model = StockExit::with('warehouse')->with('items')->find($id);
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
        $stockExit = stockExit::find($id);
        if (!$stockExit) {
            return response()->json(['message' => 'Phiếu không tồn tại'], 404);
        }
        if ($stockExit->confirmed) {
            return response()->json(['message' => 'Phiếu đã duyệt. Không thể sửa!'], 422);
        }

        // Validate
        $batch = new Batch;
        $validate = $this->applyValidate($stockExit->validate, ['name' => 'items', 'validate' => $batch->validate], 'update', ['id' => $id, 'fields' => ['code']]);
        $request->validate($validate);

        $isSuccess = $stockExit->update($request->all());
        if ($isSuccess) {
            $stockExitItemsData = $request->input('items', []);
            if (!empty($stockExitItemsData)) {
                $stockExit->items()->delete();
                foreach ($stockExitItemsData as $itemData) {
                    $itemData['stock_exit_id'] = $stockExit->id;
                    $itemData['warehouse_id'] = $stockExit->warehouse_id;
                    $itemData['date'] = $stockExit->date;

                    $batch->create($itemData);
                }
            }
            return response()->json(['message' => 'Lưu thành công', 'data' => $stockExit], 200);
        }

        return response()->json(['message' => 'Lưu thất bại'], 500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stockExit = StockExit::find($id);
        if (!$stockExit) {
            return response()->json(['message' => 'Phiếu không tồn tại'], 404);
        }
        if ($stockExit->confirmed) {
            return response()->json(['message' => 'Phiếu đã duyệt. Không thể xóa!'], 422);
        }

        $stockExit->items()->delete();
        $stockExit->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }

    public function confirm(Request $request, $id) {
        $stockExit = StockExit::find($id);
        if (!$stockExit) {
            return response()->json(['message' => 'Phiếu không tồn tại'], 404);
        }

        $request->validate(['confirmed' => 'required|boolean']);
        
        if ($request->confirmed) { // Duyệt giảm kho
            $items = $stockExit->items;
            $canFlag = true;
            $outStockItems = [];
            foreach ($items as $item) {
                $quantity = $this->inventoryService->checkInventory($item->product_id, $item->warehouse_id, ['stock_exit_id' => $stockExit->id]);
                if ($quantity < 0) {
                    $canFlag = false;
                }
                $outStockItems[] = ['product' => $item->product, 'quantityShortage' => $quantity];
            }
            if ($canFlag) {
                $stockExit->update(['confirmed' => 1]);

                foreach ($stockExit->items as $item) {
                    $item->update(['confirmed' => 1]);
                }
                return response()->json(['message' => 'Duyệt thành công', 'outStockItems' => $outStockItems]);
            } else {
                return response()->json(['message' => 'Số lượng trong kho không đủ để duyệt', 'outStockItems' => $outStockItems]);
            }
        } else {
            $stockExit->update(['confirmed' => 0]);

            foreach ($stockExit->items as $item) {
                $item->update(['confirmed' => 0]);
            }
            return response()->json(['message' => 'Hủy duyệt thành công']);
        }

    }
}
