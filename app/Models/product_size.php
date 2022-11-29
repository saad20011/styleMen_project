<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_size extends Model
{
    protected $table = 'product_size';
    use HasFactory;
    protected $fillable = [
        'product_id',
        'size_id',
        'user_id',
        'account_id'
    ];

    public function products()
    {
        return $this->belongsTo(product::class);
    }
    
    public function sizes()
    {
        return $this->belongsTo(size::class);
    }

    public function supplier_order_product_sizes()
    {
        return $this->hasMany(supplier_order_product_size::class);
        
    }

    public function product_orders()
    {
        return $this->belongsToMany(product_order::class, 'supplier_order_product_size');
        
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
