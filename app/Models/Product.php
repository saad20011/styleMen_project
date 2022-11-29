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
    
    public function suppliers()
    {
        return $this->belongsToMany(supplier::class );
    }

    public function offers()
    {
        return $this->belongsTo(offer::class );
    }

    public function account_products()
    {
        return $this->belongsTo(account_product::class );
    }
}
