<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class brand extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'website',
        'email',
        'photo',
        'photo_dir',
        'statut',
        'account_id'
    ];

    public function brand_source()
    {
        return $this->hasMany(brand_source::class);
        
    }
    public function sources()
    {
        return $this->belongsToMany(source::class);
    }

    public function offers()
    {
        return $this->hasMany(offer::class);
    }

    public function account_offers()
    {
        return $this->belongsToMany(account::class, 'offers');
    }
}
