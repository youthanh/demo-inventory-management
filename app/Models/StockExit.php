<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\InventoryService;

class StockExit extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'date', 'warehouse_id', 'customer', 'note', 'confirmed'];

    public $validate = [
        'code' => 'required|string|unique:stock_exits,code',
        'date' => 'required|date_format:Y-m-d',
        'warehouse_id' => 'required|numeric|gt:0|exists:warehouses,id',
        'customer' => 'nullable|string',
        'note' => 'nullable|string',
        'confirmed' => 'nullable|boolean',
    ];

    public function items()
    {
        return $this->hasMany(Batch::class)->select(['id', 'stock_exit_id', 'warehouse_id', 'product_id', 'quantity', 'unit_price', 'note'])->with('product');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id')->select(['id', 'code', 'name']);
    }

    public function confirm($id, $confirmed = 1) {
        $stockExit = StockExit::find($id);
        if (!$stockExit) {
            $response  = [
                'success' => false,
                'message' => 'Phiếu không tồn tại',
            ];
            return $response;
        }
        
        if ($confirmed) { // Duyệt giảm kho
            $inventoryService = new InventoryService();
            $items = $stockExit->items;
            $canFlag = true;
            $outStockItems = [];
            foreach ($items as $item) {
                $quantity = $inventoryService->checkInventory($item->product_id, $item->warehouse_id, ['stock_exit_id' => $stockExit->id]);
                if ($quantity < 0) {
                    $canFlag = false;
                    $outStockItems[] = ['product' => $item->product, 'quantityShortage' => $quantity];
                }
            }
            if ($canFlag) {
                $stockExit->update(['confirmed' => 1]);

                foreach ($stockExit->items as $item) {
                    $item->update(['confirmed' => 1]);
                }
                return ['success' => true, 'message' => 'Duyệt thành công'];
            } else {
                return ['success' => false, 'message' => 'Số lượng trong kho không đủ để duyệt', 'outStockItems' => $outStockItems];
            }
        } else {
            $stockExit->update(['confirmed' => 0]);

            foreach ($stockExit->items as $item) {
                $item->update(['confirmed' => 0]);
            }
            return ['message' => 'Hủy duyệt thành công'];
        }

    }
}
