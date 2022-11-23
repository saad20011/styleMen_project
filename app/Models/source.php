<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class source extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'photo',
        'photo_dir',
        'statut',
        'account_id'
    ];
}
