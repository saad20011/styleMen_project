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

    public function brand_sources()
    {
        return $this->hasMany(brand_source::class);
        
    }
    public function sources()
    {
        return $this->belongsToMany(source::class)
            ->wherePivot('statut', 1);
    }

    public function offers()
    {
        return $this->hasMany(offer::class);
    }

    public function images()
    {
        return $this->morphToMany(image::class, 'imageable')
            ->wherePivot('statut', 1);
    }
    public function imageables()
    {

        return $this->hasMany(imageable::class, 'imageable_id', 'id');
    }

}
