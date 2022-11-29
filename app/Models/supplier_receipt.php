<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier_receipt extends Model
{
    use HasFactory;

    public function suppliers(){

        return $this->belongsTo(supplier::class);
    }

    public function account_users(){
        
        return $this->belongsTo(account_user::class);
    }

    public function supplier_order_product_sizes()
    {
        return $this->hasMany(supplier_order_product_size::class);
        
    }

    public function product_sizes()
    {
        return $this->belongsToMany(product_size::class, 'supplier_order_product_size');
        
    }

    public function users()
    {
        return $this->belongsToMany(user::class, 'supplier_order_product_size');
        
    }

    public function supplier_order()
    {
        return $this->belongsToMany(supplier_order::class, 'supplier_order_product_size');
        
    }

}
