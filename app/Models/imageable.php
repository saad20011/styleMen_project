<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class imageable extends Model
{
    use HasFactory;
    protected $fillable = [
        'imageable_id',
        'imageable_type'
    ];
}
