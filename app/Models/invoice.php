<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;
    public function account_carrier(){
        $this->belongsTo(account_carrier::class);
    }

    public function users(){
        $this->belongsTo(User::class);
    }
}
