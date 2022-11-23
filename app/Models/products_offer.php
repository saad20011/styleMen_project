<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products_offer extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'offer_id',
    ];
}
