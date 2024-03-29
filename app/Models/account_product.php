<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class account_product extends Model
{
    use HasFactory;

    protected $table = 'account_product' ;
    protected $fillable = [
        'product_id',
        'category_id',
        'account_id'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function accounts(){
        return $this->belongsTo(account::class);
    }

    public function offers()
    {
        return $this->belongsToMany(offer::class, 'product_offer', 'account_product_id', 'offer_id');
    }

    public function activeOffers()
    {
        return $this->belongsToMany(offer::class, 'product_offer', 'account_product_id', 'offer_id')
            ->wherePivotIn('status', [1]);
    }

    public function product_offer()
    {
        return $this->hasMany(product_offer::class, );
    }
    public function products()
    {
        return $this->hasMany(product::class, 'id', 'product_id');
    }
}
