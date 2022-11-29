<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pickup extends Model
{
    use HasFactory;

    public function account_user(){
        $this->belongsTo(account_user::class);
    }

    public function account_carrier(){
        $this->belongsTo(account_carrier::class);
    }

    public function collector(){
        $this->belongsTo(collector::class);
    }
}
