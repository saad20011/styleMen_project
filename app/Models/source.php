<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class source extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'photo',
        'photo_dir',
        'statut',
        'account_id'
    ];
    public function brand_source()
    {
        return $this->hasMany(brand_source::class);
        
    }
    public function brands()
    {
        return $this->belongsToMany(brand::class, 'brand_source');
    }
}
