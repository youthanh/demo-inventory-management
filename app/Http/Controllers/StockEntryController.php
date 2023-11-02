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
        $result = $query->with('items')->paginate(request('per_page', null));
        return response()->json($result, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Lưu thông tin phiếu nhập kho
        $stockEntryData = $request->validate([
            'code' => 'required',
            'date' => 'required|date',
            'warehouse_id' => 'required',
            'items' => 'required|array',
        ]);

        $stockEntry = StockEntry::create($stockEntryData);

        // Lưu thông tin mặt hàng trong phiếu nhập kho
        $stockEntryItemsData = $request->input('items', []); // Lấy thông tin mặt hàng từ request

        foreach ($stockEntryItemsData as $itemData) {
            $itemData = $this->validateItem($itemData);
            $itemData['stock_entry_id'] = $stockEntry->id;
            Batch::create($itemData);
        }

        return response()->json(['stockEntry' => $stockEntry]);
    }

    // Hàm xác thực item
    protected function validateItem($data)
    {
        return Validator::make($data, [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:1',
        ])->validate();
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
