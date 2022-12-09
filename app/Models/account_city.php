<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class account_city extends Model
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
        return $this->belongsTo(city::class, 'city_id', 'id');
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

    public function orders(){
        return $this->hasMany(order::class);
    }

    public function has_addresses(){
        return $this->hasMany(address::class);
    }
    public function account_user_order(){
        return $this->belongsToMany(account_user::class, 'orders');
    }
    public function customer_order(){
        return $this->belongsToMany(customer::class, 'orders');
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
