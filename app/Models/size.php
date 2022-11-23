<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class size extends Model
{
    use HasFactory;
    protected $fillable = [
        'title' ,
        'statut', 
        'account_id',
        'user_id' ,
        'photo' ,
        'photo_dir',
        'type_size_id' 
    ];
}
