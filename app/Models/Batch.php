<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockEntry;
use App\Models\Product;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'product_id', 'warehouse_id', 'stock_entry_id', 'stock_exit_id', 'quantity', 'unit_price', 'note', 'confirmed'];
    
    public $validate = [
        'date' => 'required|date',
        'stock_entry_id' => 'nullable|exists:stock_entries,id',
        'stock_exit_id' => 'nullable|exists:stock_exits,id',
        'product_id' => 'required|exists:products,id',
        'warehouse_id' => 'required|exists:warehouse,id',
        'quantity' => 'required|numeric|gte:0',
        'unit_price' => 'required|numeric|gte:0',
        'note' => 'string|nullable',
    ];

    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->select(['id', 'code', 'name']);
    }
}
