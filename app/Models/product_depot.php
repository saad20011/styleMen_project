<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_depot extends Model
{
    use HasFactory;
    protected $table = 'product_depot';
    protected $fillable = [
        'product_id',
        'product_variationAttribute_id'
    ];
}
