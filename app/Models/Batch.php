<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockEntry;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'warehouse_id', 'stock_entry_id', 'stock_exit', 'quantity', 'confirmed'];

    public function stockEntry()
    {
        return $this->belongsTo(StockEntry::class);
    }
}
