<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockEntry;
use App\Models\Product;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'warehouse_id', 'stock_entry_id', 'stock_exit', 'quantity', 'confirmed'];

    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->select(['id', 'code', 'name']);
    }
}
