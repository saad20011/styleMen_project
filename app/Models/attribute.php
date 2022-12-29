<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'title' ,
        'statut', 
        'account_user_id',
        'types_attribute_id' 
    ];

    public function type_size()
    {
        return $this->belongsTo(customer::class);
    }

    public function product_variationAttribute()
    {
        return $this->hasMany(product_variationAttribute::class);
        
    }
    public function products()
    {
        return $this->belongsToMany(product::class );
    }
}
