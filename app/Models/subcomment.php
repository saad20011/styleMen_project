<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subcomment extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'account_user_id',
        'comment_id',
        'order_change'
    ];
    public function comments()
    {
        return $this->belongsTo(comment::class);
    }

    public function accounts()
    {
        return $this->belongsTo(account_user::class);
    }

    public function order_comments(){
        return $this->hasMany(order_comment::class);
    }

    public function account_user_order_comment(){
        return $this->belongsToMany(account_user::class, 'order_comments');
    }
    public function order_order_comment(){
        return $this->belongsToMany(order::class, 'order_comments');
    }
    public function status_order_comment(){
        return $this->belongsToMany(status::class, 'order_comments');
    }
}
