<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    use HasFactory;
    protected $fillable=['name','comment','facebook','note','account_id'];
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
        return $this->morphToMany(address::class, 'addressable');
    }

    // belongsTo
    public function accounts()
    {
        return $this->belongsTo(customer::class);
    }

    //order
    public function orders(){
        return $this->hasMany(order::class);
    }

    public function account_user_order(){
        return $this->belongsToMany(account_user::class, 'orders');
    }
    public function account_city_order(){
        return $this->belongsToMany(account_city::class, 'orders');
    }
    public function payment_type_order(){
        return $this->belongsToMany(payment_type::class, 'orders');
    }
    public function payment_method_order(){
        return $this->belongsToMany(payment_method::class, 'orders');
    }
    public function brand_source_order(){
        return $this->belongsToMany(brand_source::class, 'orders');
    }
    public function pickup_order(){
        return $this->belongsToMany(pickup::class, 'orders');
    }
    public function statuses_order(){
        return $this->belongsToMany(status::class, 'orders');
    }

    public function payment_commision_order(){
        return $this->belongsToMany(payment_commission::class, 'orders');
    }

    public function invoice_order(){
        return $this->belongsToMany(invoice::class, 'orders');
    }
}
