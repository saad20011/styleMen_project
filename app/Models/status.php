<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class status extends Model
{
    use HasFactory;

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
    public function customer(){
        return $this->belongsToMany(customer::class, 'orders');
    }

    public function payment_commision_order(){
        return $this->belongsToMany(payment_commission::class, 'orders');
    }

    public function invoice_order(){
        return $this->belongsToMany(invoice::class, 'orders');
    }

    public function order_comments(){
        return $this->hasMany(order_comment::class);
    }

    public function account_user_order_comment(){
        return $this->belongsToMany(account_user::class, 'order_comments');
    }
    public function subcomment_order_comment(){
        return $this->belongsToMany(subcomment::class, 'order_comments');
    }
    public function order_order_comment(){
        return $this->belongsToMany(order::class, 'order_comments');
    }
}
