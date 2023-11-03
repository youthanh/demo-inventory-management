<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'order', 'supplier', 'unit_price'];

    public $validate = [
        'code' => 'required|string|unique:products,code',
        'name' => 'required|string',
        'order' => 'string|nullable',
        'supplier' => 'string|nullable',
        'unit_price' => 'numeric|gte:0',
    ];
}
