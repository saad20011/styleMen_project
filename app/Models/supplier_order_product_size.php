<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier_order_product_size extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_order_id',
        'product_size_id',
        'receipt_id',
        'quantity',
        'price',
        'user_id',
        'status'
    ];
}
