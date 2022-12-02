<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'title',
        'email',
        'trackinglink',
        'autocode',
        'comment',
        'statut',
    ];

    public function accounts()
    {
        return $this->belongsToMany(account::class);
        
    }
    public function account_carriers()
    {
        return $this->hasMany(account_carrier::class);
        
    }
    
    public function default_carrier()
    {
        return $this->hasMany(default_carrier::class);
        
    }
    public function cities()
    {
        return $this->belongsToMany(city::class, 'default_carriers');
    }
}
