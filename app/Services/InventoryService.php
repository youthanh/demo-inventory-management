<?php

namespace App\Services;

use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function checkInventory($productId, $warehouseId, array $refferenceId = [])
    {
        $query = DB::table('batches')
            ->selectRaw('COALESCE(SUM(IF(stock_exit_id IS NOT NULL, (quantity * -1), quantity)), 0) as quantity')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where(function ($query) use ($refferenceId) {
                $query->where('confirmed', 1);

                if (!empty($refferenceId['stock_exit_id'])) {
                    // $query->orWhere('stock_exit_id', $refferenceId['stock_exit_id']);
                    $query->orWhere(function ($query) use ($refferenceId) {
                        $query->whereNotNull('confirmed');
                        $query->where('stock_exit_id', $refferenceId['stock_exit_id']);
                    });
                }
            });

        if (!empty($refferenceId['stock_entry_id'])) {
            $query->where(function ($query) use ($refferenceId) {
                $query->where('stock_entry_id', '<>', $refferenceId['stock_entry_id']);
                $query->orWhereNull('stock_entry_id');
            });
        }

        $result = $query->first()->quantity;
        return $result;
    }
}
