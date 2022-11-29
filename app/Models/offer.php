<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class offer extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'price', 'shipping_price', 'statut', 'brand_id','account_id'
    ];

    public function brands()
    {
        return $this->belongsTo(brand::class);
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
}
