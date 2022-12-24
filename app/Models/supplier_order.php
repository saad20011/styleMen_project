<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier_order extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'shipping_date',
        'supplier_id',
        'account_user_id',
        'status'
    ];

    public function suppliers()
    {
        return $this->belongsTo(supplier::class, 'supplier_id', 'id');
        
    }

    public function supplier_order_product_size()
    {
        return $this->hasMany(supplier_order_product_size::class, 'supplier_order_id', 'id');
        
    }

    public function product_sizes()
    {
        return $this->belongsToMany(product_size::class, 'supplier_order_product_size');
        
    }

    public function users()
    {
        return $this->belongsToMany(user::class, 'supplier_order_product_size');
        
    }

    public function supplier_receipts()
    {
        return $this->belongsToMany(supplier_receipt::class, 'supplier_order_product_size');
        
    }
}
