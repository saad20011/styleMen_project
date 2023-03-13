<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categorie extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title' ,
        'statut' ,
        'photo' , 
        'photo_dir',
        'account_id',
        'user_id' 
    ];
    public function products()
    {
        return $this->belongsToMany(product::class , 'account_product', 'category_id', 'product_id', 'id');
    }}
