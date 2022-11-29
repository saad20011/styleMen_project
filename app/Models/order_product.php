<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_product extends Model
{
    use HasFactory;
    public function account_users(){
        return $this->belongsTo(account_user::class);
    }
    public function orders(){
        return $this->belongsTo(order::class);
    }
    public function product_sizes(){
        return $this->belongsTo(product_size::class);
    }
    public function offers(){
        return $this->belongsTo(offer::class);
    }

}
