<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'title',
        'photo',
        'photo_dir',
        'statut'
    ];
    
    public function accounts()
    {
        return $this->morphedByMany(account::class, 'imageable');
    }
    public function imageables()
    {
        return $this->hasMany(imageable::class, 'image_id', 'id');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'imageable');
    }

    public function suppliers()
    {
        return $this->morphedByMany(supplier::class, 'imageable');
    }

    public function customers()
    {
        return $this->morphedByMany(customer::class, 'imageable');
    }

    public function collectors()
    {
        return $this->morphedByMany(collector::class, 'imageable');
    }
    
    public function brands()
    {
        return $this->morphedByMany(brand::class, 'imageable');
    }
}
