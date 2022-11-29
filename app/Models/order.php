<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;

    public function customer(){
        return $this->belongsTo(customer::class);
    }

    public function account_user(){
        return $this->belongsTo(account_user::class);
    }
    public function account_city(){
        return $this->belongsTo(account_city::class);
    }
    public function payment_type(){
        return $this->belongsTo(payment_type::class);
    }
    public function payment_method(){
        return $this->belongsTo(payment_method::class);
    }
    public function brand_source(){
        return $this->belongsTo(brand_source::class);
    }
    public function pickup(){
        return $this->belongsTo(pickup::class);
    }
    public function statuses(){
        return $this->belongsTo(status::class);
    }

    public function payment_commision(){
        return $this->belongsTo(payment_commission::class);
    }

    public function invoice(){
        return $this->belongsTo(invoice::class);
    }

}
