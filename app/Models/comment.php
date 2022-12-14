<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'statut',
        'current_statut',
        'post_poned',
        'account_id'
    ];
    public function accounts()
    {
        return $this->belongsTo(account::class);
    }

    public function account_user_subcomments()
    {
        return $this->belongsToMany(account_user::class, 'subcomments')
            ->withPivot('title')
        ;
    }

    public function subcomments()
    {
        return $this->hasMany(subcomment::class);
    }
}
