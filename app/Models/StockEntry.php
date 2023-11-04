<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Batch;
use App\Models\Warehouse;

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
}
