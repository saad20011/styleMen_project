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
        'statut',
    ];
    // belongsToMany

    public function cities()
    {
        return $this->belongsToMany(city::class, 'account_city')
        ->withPivot('id');
    }

    public function carriers()
    {
        return $this->belongsToMany(carrier::class, 'account_carrier');
        
    }

    public function has_carriers()
    {
        return $this->hasMany(carrier::class);
        
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function products()
    {
        return $this->belongsToMany(product::class, 'account_product');
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
        return $this->morphToMany(address::class, 'addressable');
    }

    // hasMany

    public function account_carriers()
    {
        return $this->hasMany(account_carrier::class);
        
    }

    public function account_codes()
    {
        return $this->hasMany(account_code::class);
        
    }

    public function account_user()
    {
        return $this->hasMany(account_user::class, 'account_id', 'id');
    }  
    
    public function account_product()
    {
        return $this->hasMany(account_product::class);
        
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
    public function brands()
    {
        return $this->hasMany(brand::class);
    }
    public function sources()
    {
        return $this->hasMany(source::class);
    }
    public function has_images()
    {
        return $this->hasMany(image::class);
    }
    public function has_phones()
    {
        return $this->hasMany(phone::class);
    }
    public function has_addresses()
    {
        return $this->hasMany(address::class);
    }
}
