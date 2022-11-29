<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class payment_type extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name'
    ];

    public function payment_commissions()
    {
        return $this->hasMany(payment_commission::class);
    }
}
