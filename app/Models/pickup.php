<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pickup extends Model
{
    use HasFactory;

    public function account_user(){
        $this->belongsTo(account_user::class);
    }

    public function account_carrier(){
        $this->belongsTo(account_carrier::class);
    }

    public function collector(){
        $this->belongsTo(collector::class);
    }

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
    public function customer_order(){
        return $this->belongsToMany(customer::class, 'orders');
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
