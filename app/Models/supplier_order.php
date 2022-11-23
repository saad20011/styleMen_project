<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier_order extends Model
{
    use HasFactory;
    protected $fillable = [
        'reference',
        'shipping_date',
        'supplier_id',
        'account_id',
        'user_id',
        'status'
    ];
}
