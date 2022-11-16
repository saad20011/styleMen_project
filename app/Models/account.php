<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prefixe',
        'counter',
        'statut',
        'photo',
        'photo_dir'
    ];
}
