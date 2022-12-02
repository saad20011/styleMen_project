<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class city extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'statut',
        'region_id',
        'preferred',
    ];
    public function accounts()
    {
        return $this->belongsToMany(account::class, 'account_city');
        
    }
    public function regions()
    {
        return $this->belongsTo(region::class,'region_id');
        
    }
    public function account_city()
    {
        return $this->hasMany(account_city::class);
        
    }
    public function default_carrier()
    {
        return $this->hasMany(default_carrier::class);
        
    }
    public function carriers()
    {
        return $this->belongsToMany(carrier::class, 'default_carriers');
    }
}
