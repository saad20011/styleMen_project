<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class charge extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'charge_type_id',
        'montant',
        'payment_commission_id',
        'comment',
        'date',
        'statut',
    ];
}
