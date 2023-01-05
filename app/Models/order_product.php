<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_product extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_user_id',
        'order_id',
        'product_variationattribute_id',
        'offer_id',
        'price',
        'quantity',
        'statut ',
    ];
    public function account_users(){
        return $this->belongsTo(account_user::class);
    }
    public function orders(){
        return $this->belongsTo(order::class);
    }
    public function product_variationAttributes(){
        return $this->belongsTo(product_variationAttribute::class, 'product_variationattribute');
    }
    public function offers(){
        return $this->belongsTo(offer::class);
    }

}
