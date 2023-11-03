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
        $result = $query->with('warehouse')->with('items')->simplePaginate(request('per_page', null));
        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lưu thông tin phiếu nhập kho
        $stockEntryData = $request->validate([
            'code' => 'required|unique:stock_entries,code',
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array',
            'note' => 'string|nullable',
        ]);

        $stockEntry = StockEntry::create($stockEntryData);

        if (!empty($stockEntry->id)) {
            // Lưu thông tin mặt hàng trong phiếu nhập kho
            $stockEntryItemsData = $request->input('items', []); // Lấy thông tin mặt hàng từ request
    
            foreach ($stockEntryItemsData as $itemData) {
                $itemData['stock_entry_id'] = $stockEntry->id;
                $itemData['warehouse_id'] = $stockEntry->warehouse_id;
                $itemData = $this->validateItem($itemData);
                Batch::create($itemData);
            }
        }

        return response()->json(['stockEntry' => $stockEntry]);
    }

    // Hàm xác thực item
    protected function validateItem($data)
    {
        return Validator::make($data, [
            'stock_entry_id' => 'required|exists:stock_entries,id',
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:1',
            'note' => 'string|nullable',
        ])->validate();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Tìm sản phẩm dựa trên ID
        $model = StockEntry::with('warehouse')->with('items')->find($id);

        // Kiểm tra xem sản phẩm có tồn tại không
        if (!$model) {
            return response()->json(['message' => 'Phiếu không tồn tại'], 404);
        }

        // Trả về thông tin chi tiết của sản phẩm
        return response()->json($model, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $model = StockEntry::find($id);

        if (!$model) {
            return response()->json(['message' => 'Phiếu không tồn tại'], 404);
        }

        $stockEntryData = $request->validate([
            'code' => 'required|unique:stock_entries,code,'.$id,
            'date' => 'required|date',
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array',
            'note' => 'string|nullable',
        ]);
        $stockEntry = $model->update($stockEntryData);

        if (!empty($stockEntry->id)) {
            // Lưu thông tin mặt hàng trong phiếu nhập kho
            $stockEntryItemsData = $request->input('items', []); // Lấy thông tin mặt hàng từ request
    
            foreach ($stockEntryItemsData as $itemData) {
                $itemData['stock_entry_id'] = $stockEntry->id;
                $itemData['warehouse_id'] = $stockEntry->warehouse_id;
                $itemData = $this->validateItem($itemData);
                Batch::create($itemData);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}