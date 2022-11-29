<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_comment extends Model
{
    use HasFactory;

    public function orders(){
        return $this->belongsTo(order::class);
    }
    public function subcomments(){
        return $this->belongsTo(subcomment::class);
    }
    public function statuses(){
        return $this->belongsTo(status::class);
    }
    public function account_users(){
        return $this->belongsTo(account_user::class);
    }
}
