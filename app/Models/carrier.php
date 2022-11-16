<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'phone_id',
        'adresse_id',
        'email',
        'trackinglink',
        'autocode',
        'photo',
        'photo_dir',
        'comment',
        'statut',
        'user_id'
    ];
}
