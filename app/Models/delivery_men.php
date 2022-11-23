<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class delivery_men extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'account_carrier_id',
        'photo',
        'photo_dir',
        'statut'
    ];
}
