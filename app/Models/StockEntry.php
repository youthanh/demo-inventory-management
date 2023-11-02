<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Batch;

class StockEntry extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'date', 'warehouse_id', 'note'];

    public function items()
    {
        return $this->hasMany(Batch::class);
    }
}
