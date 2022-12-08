<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'reference', 'title','link','price', 'sellingprice','account_user_id','account_id',
        'photo','photo_dir'
    ];

    public function account_users(){
        return $this->belongsTo(account_user::class);
    }

    public function product_size()
    {
        return $this->hasMany(product_size::class);
        
    }

    public function product_supplier()
    {
        return $this->hasMany(product_supplier::class);
        
  
    }
    public function sizes()
    {
        return $this->belongsToMany(size::class );
    }
    public function depots()
    {
        return $this->belongsToMany(depot::class, 'product_depot' );
    }
    public function suppliers()
    {
        return $this->belongsToMany(supplier::class );
    }

    public function offers()
    {
        return $this->belongsToMany(offer::class, 'product_offer' );
    }

    public function account_product()
    {
        return $this->hasMany(account_product::class);
    }

    public function images()
    {
        return $this->morphToMany(image::class, 'imageable')
            ->wherePivotIn('statut', [1,2])
            ->withPivot('statut');
    }
    public function has_images()
    {
        return $this->hasMany(image::class);
    }
    public function imageables()
    {

        return $this->hasMany(imageable::class, 'imageable_id', 'id');
    }
}
