<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    
    protected $fillable = ['code', 'name', 'address'];

    public $validate = [
        'code' => 'required|string|unique:warehouses,code',
        'name' => 'required|string',
        'address' => 'nullable|string',
    ];
}
