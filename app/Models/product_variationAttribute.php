<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_variationAttribute extends Model
{
    protected $table = 'product_variationAttribute';
    use HasFactory;
    protected $fillable = [
        'product_id',
        'attribute_id',
        'user_id',
        'account_id'
    ];

    public function products()
    {
        return $this->belongsTo(product::class, 'product_id', 'id');
    }

    public function variationAttributes()
    {
        return $this->belongsTo(variationAttribute::class, 'variationAttribute_id', 'id');
    }

    public function supplier_order_product_variationAttribute()
    {
        return $this->hasMany(supplier_order_product_variationAttribute::class, 'product_variationAttribute_id', 'supplier_order_product_variationAttribute_id');
        
    }
    public function product_depot()
    {
        return $this->hasMany(product_depot::class);
        
    }
    public function depots()
    {
        return $this->belongsToMany(depot::class, 'product_depot', 'product_variationAttribute_id', '', 'id');
        
    }
    public function product_orders()
    {
        return $this->belongsToMany(product_order::class, 'supplier_order_product_attribute');
        
    }

    public function users()
    {
        return $this->belongsToMany(user::class, 'supplier_order_product_attribute');
        
    }

    public function supplier_receipts()
    {
        return $this->belongsToMany(supplier_receipt::class, 'supplier_order_product_attribute');
        
    }

    public function order_products(){
        return $this->hasMany(order_product::class);
    }
    


    public function order_order_product(){
        return $this->belongsToMany(order::class, 'order_products');
    }
    public function account_user_order_product(){
        return $this->belongsToMany(account_user::class, 'order_products');
    }
    public function offer_order_product(){
        return $this->belongsToMany(offer::class, 'order_products');
    }
}
