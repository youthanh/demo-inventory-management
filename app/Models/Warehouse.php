<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'address'];

    public $validate = [
        'code' => 'required|string|unique:warehouses,code',
        'name' => 'required|string',
        'address' => 'nullable|string',
    ];

    public function inventory()
    {
        $query = DB::table('batches')
            ->selectRaw('COALESCE(SUM(IF(stock_exit_id IS NOT NULL, (quantity * -1), quantity)), 0) as quantity')
            ->selectRaw('product_id')
            ->where('warehouse_id', $this->id)
            ->where('confirmed', 1)
            ->havingRaw('quantity <> 0')
            ->groupBy('warehouse_id')
            ->groupBy('product_id');
        $result = $query->get();

        foreach ($result as $index => $row) {
            $product = Product::where('id', $row->product_id)->select(['id', 'code', 'name', 'unit_price'])->get();
            $result[$index]->product = $product;
        }

        return $result;
    }
}
