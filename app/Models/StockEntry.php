<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Batch;
use App\Models\Warehouse;
use App\Services\InventoryService;

class StockEntry extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'date', 'warehouse_id', 'supplier', 'note', 'confirmed'];

    public $validate = [
        'code' => 'required|string|unique:stock_entries,code',
        'date' => 'required|date_format:Y-m-d',
        'warehouse_id' => 'required|numeric|gt:0|exists:warehouses,id',
        'supplier' => 'nullable|string',
        'note' => 'nullable|string',
        'confirmed' => 'nullable|boolean',
    ];

    public function items()
    {
        return $this->hasMany(Batch::class)->select(['id', 'stock_entry_id', 'warehouse_id', 'product_id', 'quantity', 'unit_price', 'note'])->with('product');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id')->select(['id', 'code', 'name']);
    }

    public function confirm($id, $confirmed = 1) {
        $stockEntry = $this->find($id);
        if (!$stockEntry) {
            $response  = [
                'success' => false,
                'message' => 'Phiếu không tồn tại',
            ];
            return $response;
        }

        if (!$confirmed) { // Bỏ duyệt kho
            $items = $stockEntry->items;
            $canFlag = true;
            $outStockItems = [];
            $inventoryService = new InventoryService();
            foreach ($items as $item) {
                $quantity = $inventoryService->checkInventory($item->product_id, $item->warehouse_id, ['stock_entry_id' => $stockEntry->id]);
                if ($quantity < 0) {
                    $canFlag = false;
                    $outStockItems[] = ['product' => $item->product, 'quantityShortage' => $quantity];
                }
            }
            if ($canFlag) {
                $stockEntry->update(['confirmed' => 0]);

                foreach ($stockEntry->items as $item) {
                    $item->update(['confirmed' => 0]);
                }
                return ['success' => true, 'message' => 'Hủy duyệt thành công'];
            } else {
                return ['success' => false, 'message' => 'Số lượng trong kho không đủ.', 'outStockItems' => $outStockItems];
            }
        } else {
            $stockEntry->update(['confirmed' => 1]);

            foreach ($stockEntry->items as $item) {
                $item->update(['confirmed' => 1]);
            }
            return ['success' => true, 'message' => 'Duyệt thành công'];
        }

    }
}
