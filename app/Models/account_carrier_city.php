<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class accounts_carriers_city extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_carrier_id',
        'account_city_id',
        'name',
        'price',
        'return',
        'delivery_time',
        'statut'
    ];
    public function account_carriers(){
        return $this->belongsTo(account_carrier::class);
    }

    public function account_cities(){
        return $this->belongsTo(account_city::class);
    }
}
