<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier_receipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'supplier_id',
        'account_user_id',
        'statut'
    ];
    
    public function suppliers(){

        return $this->belongsTo(supplier::class, 'supplier_id', 'id');
    }

    public function account_users(){
        
        return $this->belongsTo(account_user::class);
    }

    public function supplier_order_product_variationAttributes()
    {
        return $this->hasMany(supplier_order_product_variationAttribute::class);
        
    }

    public function product_variationAttributes()
    {
        return $this->belongsToMany(product_variationAttribute::class, 'supplier_order_product_variationAttribute');
        
    }

    public function users()
    {
        return $this->belongsToMany(user::class, 'supplier_order_product_variationAttribute');
        
    }

    public function supplier_order()
    {
        return $this->belongsToMany(supplier_order::class, 'supplier_order_product_variationAttribute');
        
    }

}
