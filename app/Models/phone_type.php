<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class phone_type extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'status',
        'account_id'
    ];
}
