<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class size extends Model
{
    use HasFactory;
    protected $fillable = [
        'title' ,
        'statut', 
        'account_id',
        'user_id' ,
        'photo' ,
        'photo_dir',
        'type_size_id' 
    ];

    public function type_size()
    {
        return $this->belongsTo(customer::class);
    }

    public function product_size()
    {
        return $this->hasMany(product_size::class);
        
    }
    public function products()
    {
        return $this->belongsToMany(product::class );
    }
}
