<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class account_carrier_city extends Model
{
    use HasFactory;
    protected $table = 'account_carrier_city' ;

    protected $fillable = [
        'account_carrier_id',
        'account_city_id',
        'name',
        'price',
        'return',
        'delivery_time',
        'statut'
    ];
    public function account_carrier(){
        return $this->belongsTo(account_carrier::class);
    }

    public function account_citie(){
        return $this->belongsTo(account_city::class);
    }
}
