<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class brand_source extends Model
{
    use HasFactory;
    protected $table = 'brand_source';
    protected $fillable = [

        'account_id',
        'source_id',
        'brand_id',
        'statut'
    ];
    public function brands(){
        return $this->belongsTo(brand::class);
    }

    public function accounts(){
        return $this->belongsTo(account::class);
    }
}
