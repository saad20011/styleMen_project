<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'account_id',
        'statut',
    ];

    public function phones()
    {
        return $this->morphToMany(phone::class, 'phoneable');
    }
    public function has_phones()
    {
        return $this->hasMany(phone::class);
    }
    public function images()
    {
        return $this->morphToMany(image::class, 'imageable');
    }

    public function has_images()
    {
        return $this->hasMany(image::class);
    }

    public function addresses()
    {
        return $this->morphToMany(address::class, 'addressable');
    }

    public function accounts()
    {
        return $this->belongsTo(account::class);
    }

    public function product_supplier()
    {
        return $this->hasMany(product_supplier::class);
        
    }
    public function products()
    {
        return $this->belongsToMany(product::class, 'product_supplier');
        
    }

    public function supplier_billings()
    {
        return $this->hasMany(supplier_billing::class);
        
    }
    public function account_users_supplier_billing()
    {
        return $this->belongsToMany(account_user::class, 'supplier_billings')
        ->withPivot('code', 'montant', 'statut');
        
    }

    
    public function supplier_orders()
    {
        return $this->hasMany(supplier_order::class);
        
    }
    public function account_users_supplier_order()
    {
        return $this->belongsToMany(account_user::class, 'supplier_orders')
        ->withPivot('code', 'shipping_date', 'statut');
        
    }

    public function supplier_receipts()
    {
        return $this->hasMany(supplier_receipt::class);
        
    }
    public function account_users_supplier_receipt()
    {
        return $this->belongsToMany(account_user::class, 'supplier_receipts')
        ->withPivot('code', 'statut');
        
    }
}
