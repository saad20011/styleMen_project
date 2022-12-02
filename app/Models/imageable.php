<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class imageable extends Model
{
    use HasFactory;
    protected $fillable = [
        'image_id',
        'statut',
        'imageable_id',
        'imageable_type'
    ];
    public function brands(){
        return $this->belongsTo(brand::class, 'imageable_id','id');
    }
}
