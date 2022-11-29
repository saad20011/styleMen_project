<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class accounts_city extends Model
{
    use HasFactory;
    protected $table = 'account_city' ;

    protected $fillable=[
        'city_id',
        'account_id',
        'prefered',
        'statut',
        'created_at',
    	'updated_at'	
    ];
    public function cities(){
        return $this->belongsTo(city::class);
    }

    public function accounts(){
        return $this->belongsTo(account::class);
    }

    public function account_carriers()
    {
        return $this->belongsToMany(account_carrier::class, 'account_carrier_city');
        
    }
    public function account_carrier_account_city()
    {
        return $this->hasMany(account_carrier_city::class);
        
    }
}
