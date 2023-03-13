<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'reference','statut', 'title','link','price', 'sellingprice','account_user_id','account_id',
    ];

    public function account_users(){
        return $this->belongsTo(account_user::class);
    }

    public function product_variationAttribute()
    {
        return $this->hasMany(product_variationAttribute::class);
        
    }

    public function product_supplier()
    {
        return $this->hasMany(product_supplier::class);
        

    }
    public function variationAttributes()
    {
        return $this->belongsToMany(variationAttribute::class , 'product_variationattribute');
    }

    public function suppliers()
    {
        return $this->belongsToMany(supplier::class)
            ;
        
    }

    public function activeSuppliers()
    {
        return $this->belongsToMany(supplier::class)
            ->wherePivotIn('status', [1])
            ->withPivot('price');

    }
    public function offers()
    {
        return $this->belongsToMany(offer::class, 'product_offer' );
    }
    public function categories()
    {
        return $this->belongsToMany(categorie::class, 'account_product', 'product_id', 'category_id' );
    }
    public function depots()
    {
        return $this->belongsToMany(depot::class, 'product_depot' );
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
    public function principal_images()
    {
        return $this->morphToMany(image::class, 'imageable')
            ->wherePivotIn('statut', [2])
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
