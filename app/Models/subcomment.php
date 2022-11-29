<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subcomment extends Model
{
    use HasFactory;
    
    public function comments()
    {
        return $this->belongsTo(comment::class);
    }

    public function accounts()
    {
        return $this->belongsTo(account_user::class);
    }
}
