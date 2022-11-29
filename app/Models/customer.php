<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    use HasFactory;

    public function phones()
    {
        return $this->morphToMany(phone::class, 'phoneable');
    }
    public function images()
    {
        return $this->morphToMany(image::class, 'imageable');
    }

    public function addresses()
    {
        return $this->morphToMany(addresse::class, 'addressable');
    }

    // belongsTo
    public function accounts()
    {
        return $this->belongsTo(customer::class);
    }

    //order
    public function customers(){
        return $this->hasMany(orders::class);
    }

    public function account_user(){
        return $this->belongsToMany(account_user::class, 'orders');
    }
    public function account_city(){
        return $this->belongsToMany(account_city::class, 'orders');
    }
    public function payment_type(){
        return $this->belongsToMany(payment_type::class, 'orders');
    }
    public function payment_method(){
        return $this->belongsToMany(payment_method::class, 'orders');
    }
    public function brand_source(){
        return $this->belongsToMany(brand_source::class, 'orders');
    }
    public function pickup(){
        return $this->belongsToMany(pickup::class, 'orders');
    }
    public function statuses(){
        return $this->belongsToMany(status::class, 'orders');
    }

    public function payment_commision(){
        return $this->belongsToMany(payment_commission::class, 'orders');
    }

    public function invoice(){
        return $this->belongsToMany(invoice::class, 'orders');
    }
}
