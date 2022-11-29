<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class collector extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'account_carrier_id',
        'photo',
        'photo_dir',
        'statut'
    ];
    public function phones()
    {
        return $this->morphToMany(phone::class, 'phoneable');
    }
    public function images()
    {
        return $this->morphToMany(image::class, 'imageable');
    }

    public function accounts()
    {
        return $this->belongsTo(account::class);
    }

    public function pickups()
    {
        return $this->hasMany(pickup::class);
    }

    public function account_carrier()
    {
        return $this->belongsToMany(account_carrier::class, 'pickups')
        ->withPivot('code')
        ;
    }

    public function account_user()
    {
        return $this->belongsToMany(account_user::class, 'pickups')
        ->withPivot('code');

    }
    
}
