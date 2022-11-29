<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Model\region;

class account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prefixe',
        'counter',
        'statut',
        'photo',
        'photo_dir'
    ];
    // belongsToMany

    public function cities()
    {
        return $this->belongsToMany(city::class);
    }

    public function carriers()
    {
        return $this->belongsToMany(carrier::class);
        
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function products()
    {
        return $this->belongsToMany(product::class);
    }

    public function brand_offers()
    {
        return $this->belongsToMany(brand::class, 'offers');
    }

    // morphToMany

    public function phones()
    {
        return $this->morphToMany(phone::class, 'phoneable');
    }
    public function images()
    {
        return $this->morphToMany(image::class, 'imageable');
    }

    public function addresses()
    {
        return $this->morphToMany(addresse::class, 'addressable');
    }

    // hasMany

    public function account_carriers()
    {
        return $this->hasMany(account_carrier::class);
        
    }

    public function account_user()
    {
        return $this->hasMany(account_user::class);
    }  
    
    public function account_product()
    {
        return $this->hasMany(account_carrier::class);
        
    }
    public function account_city()
    {
        return $this->hasMany(account_city::class);
        
    }
    public function phone_types()
    {
        return $this->hasMany(account_user::class);
    }
    public function customers()
    {
        return $this->hasMany(customer::class);
    }
    public function type_sizes()
    {
        return $this->hasMany(type_size::class);
    }

    public function depots()
    {
        return $this->hasMany(depot::class);
    }
    public function suppliers()
    {
        return $this->hasMany(supplier::class);
    }
    public function offers()
    {
        return $this->hasMany(offer::class);
    }

    public function comments()
    {
        return $this->hasMany(comment::class);
    }

    public function collectors()
    {
        return $this->hasMany(collector::class);
    }
}
