<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier_order_product_size extends Model
{
    use HasFactory;
    protected $table = 'supplier_order_product_size';
    protected $fillable = [
        'supplier_order_id',
        'supplier_receipt_id',
        'product_size_id',
        'receipt_id',
        'quantity',
        'price',
        'user_id',
        'status'
    ];

    public function supplier_orders(){
        return $this->belongsTo(supplier_order::class);
    }

    public function supplier_receipts(){
        return $this->belongsTo(supplier_receipt::class);
    }

    public function product_sizes(){
        return $this->belongsTo(product_size::class, 'product_size_id', 'id');
    }
}
