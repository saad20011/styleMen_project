<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class variationattribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_user_id',
        'variationAttribute_id',
        'attribute_id',
        'statut'
    ];  

    public function variationAttributes(){
        return $this->hasMany( variationattribute::class, 'variationAttribute_id', 'id');
    }
    public function product_variationAttributes(){
        return $this->hasMany( product_variationAttribute::class, 'variationAttribute_id', 'id');
    }

    public function attributes(){
        return $this->belongsToMany( attribute::class, 'variationAttributes');
    }
    public function products(){
        return $this->belongsToMany( product::class, 'product_variationattribute');
    }
}
