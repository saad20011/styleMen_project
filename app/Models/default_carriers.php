<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class default_carriers extends Model
{
    use HasFactory;
    public function carriers(){
        return $this->belongsTo(carrier::class);
    }

    public function cities(){
        return $this->belongsTo(city::class);
    }
}
