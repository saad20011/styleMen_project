<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class types_attribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'statut'
    ];

    public function accounts()
    {
        return $this->belongsTo(account_user::class);
    }

    public function attributes()
    {
        return $this->hasMany(attribute::class);
    }
}
