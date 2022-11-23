<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class brands_sources extends Model
{
    use HasFactory;
    protected $fillable = [
        
        'account_id',
        'source_id',
        'brand_id',
        'link',
        'statut'
    ];
}
