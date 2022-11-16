<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class accounts_city extends Model
{
    use HasFactory;
    protected $fillable=[
        'city_id',
        'account_id',
        'prefered',
        'statut',
        'created_at',
    	'updated_at'	
    ];
}
