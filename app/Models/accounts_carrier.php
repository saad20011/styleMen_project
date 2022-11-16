<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class accounts_carrier extends Model
{
    use HasFactory;
    protected $fillable=[
        'carrier_id',
        'account_id',
        'autocode',
        'statut'
    ];
}
