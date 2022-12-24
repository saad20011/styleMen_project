<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_supplier extends Model
{
    protected $table = 'product_supplier';
    use HasFactory;
    protected $fillable =[
        'product_id',
        'supplier_id',
        'account_id'
    ];

    public function products()
    {
        return $this->belongsTo(product::class);
    }
    
    public function suppliers()
    {
        return $this->belongsTo(supplier::class);
    }
}
