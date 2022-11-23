<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class type_size extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'account_id',
        'user_id'
    ];
}
