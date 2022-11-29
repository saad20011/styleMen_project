<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_commission extends Model
{
    use HasFactory;

    public function payment_types(){
        return $this->belongsTo(payment_type::class);
    }

    public function payment_methods(){
        return $this->belongsTo(payment_method::class);
    }

    public function account_users(){
        return $this->belongsTo(account_user::class);
    }
}
