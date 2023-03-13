<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_offer extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_product_id',
        'offer_id',
    ];
    protected $table = 'product_offer';
    
    public function account_product(){
        return $this->belongsTo(account_product::class);
    }

    public function offers(){
        return $this->belongsTo(offer::class, 'offer_id', 'id');
    }

}
