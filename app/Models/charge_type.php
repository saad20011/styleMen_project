<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class charge_type extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'statut'
    ];

    public function charges()
    {
        return $this->belongsTo(charge::class);
    }
}
