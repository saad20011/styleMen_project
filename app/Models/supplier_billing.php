<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class supplier_billing extends Model
{
    use HasFactory;

    public function suppliers()
    {
        return $this->belongsTo(supplier::class);
        
    }

    public function account_users()
    {
        return $this->belongsTo(account_user::class);
        
    }
}
