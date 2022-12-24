<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class address extends Model
{
    use HasFactory;
    protected $fillable = [
        'title','account_city_id'
    ] ;
    
    public function accounts()
    {
        return $this->morphedByMany(account::class, 'addressable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'addressable');
    }

    public function suppliers()
    {
        return $this->morphedByMany(supplier::class, 'addressable');
    }

    public function customers()
    {
        return $this->morphedByMany(customer::class, 'addressable');
    }
    public function account_city()
    {
        return $this->belongsTo(account_city::class);
    }
}
