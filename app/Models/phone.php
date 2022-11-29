<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class phone extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_type_id',
        'account_id',
        'title'
    ];

    public function accounts()
    {
        return $this->morphedByMany(account::class, 'phoneable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'phoneable');
    }

    public function suppliers()
    {
        return $this->morphedByMany(supplier::class, 'phoneable');
    }

    public function customers()
    {
        return $this->morphedByMany(customer::class, 'phoneable');
    }
    public function collectors()
    {
        return $this->morphedByMany(collector::class, 'phoneable');
    }
    
}
