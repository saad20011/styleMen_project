<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'phone_id',
        'adresse_id',
        'account_id',
        'photo',
        'photo_dir',
        'statut',
    ];
}
