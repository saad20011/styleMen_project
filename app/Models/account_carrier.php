<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class account_carrier extends Model
{
    use HasFactory;
    // protected $fillable = [
    //     'autocode',
    //     'statut'
    // ];
    protected $table = 'account_carrier' ;


    public function carriers(){
        return $this->belongsTo(carrier::class);
    }

    public function accounts(){
        return $this->belongsTo(account::class);
    }

    public function account_city()
    {
        return $this->belongsToMany(account_city::class, 'account_carrier_city');
        
    }

    public function account_carrier_city()
    {
        return $this->hasMany(account_carrier_city::class);
        
    }

    public function pickups()
    {
        return $this->hasMany(pickup::class);
    }

    public function account_user()
    {
        return $this->belongsToMany(account_user::class, 'pickups')
        ->withPivot('code')
        ;
    }

    public function collectors()
    {
        return $this->belongsToMany(collector::class, 'pickups')
        ->withPivot('code');

    }

    public function invoices(){
        $this->hasMany(invoice::class);
    }

    public function user_invoice(){
        $this->belongsToMany(User::class, 'invoices');
    }
}
