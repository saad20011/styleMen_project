<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class region extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'statut'
    ];
    
/**
 * Get the user that owns the region
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
    public function cities()
    {
        return $this->hasMany(city::class);
    }
}

