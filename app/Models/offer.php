<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class offer extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'price', 'shipping_price', 'statut', 'brand_id','account_id', 'offer_id'
    ];

    public function offers(){
        return $this->hasMany(offer::class );
    }
    
    public function brands()
    {
        return $this->hasMany(brand::class, 'id', 'brand_id');
    }

    public function accounts()
    {
        return $this->belongsTo(account::class);
    }

    public function account_products()
    {
        return $this->belongsToMany(account_product::class, 'product_offer');
    }

    public function product_offer()
    {
        return $this->hasMany(product_offer::class);
    }

    public function order_products(){
        return $this->hasMany(order_product::class);
    }

    public function order_order_product(){
        return $this->belongsToMany(order::class, 'order_products');
    }
    public function product_variationAttribute_order_product(){
        return $this->belongsToMany(product_variationAttribute::class, 'order_products');
    }
    public function account_user_order_product(){
        return $this->belongsToMany(account_user::class, 'order_products');
    }
}
