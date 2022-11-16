<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'reference', 'title','link','price', 'sellingprice','account_user_id',
        'photo','photo_dir'
    ];
}
