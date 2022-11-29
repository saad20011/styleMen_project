<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class account_user extends Model
{
    use HasFactory;
    protected $table = 'account_user';
    protected $fillable = [
        'account_id',
        'user_id',
        'statut'
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function accounts(){
        return $this->belongsTo(account::class);
    }

    public function payment_commissions(){
        return $this->hasMany(payment_commission::class);
    }

    public function products(){
        return $this->hasMany(product::class);
    }

    public function comment_subcomments()
    {
        return $this->belongsToMany(comment::class, 'subcomments')
            ->withPivot('title')
        ;
    }

    public function subcomments()
    {
        return $this->hasMany(subcomment::class);
    }

    public function pickups()
    {
        return $this->hasMany(pickup::class);
    }

    public function account_carrier()
    {
        return $this->belongsToMany(account_carrier::class, 'pickups')
        ->withPivot('code')
        ;
    }

    public function collectors()
    {
        return $this->belongsToMany(collector::class, 'pickups')
        ->withPivot('code');

    }

    
    public function invoices(){
        $this->hasMany(invoice::class);
    }

    public function account_carrier_invoice(){
        $this->belongsToMany(account_carrier::class, 'invoices');
    }

    public function supplier_billings()
    {
        return $this->hasMany(supplier_billing::class);
        
    }
    public function suppliers_supplier_billing()
    {
        return $this->belongsToMany(supplier::class, 'supplier_billings')
        ->withPivot('code', 'montant', 'statut');
        
    }

    public function supplier_orders()
    {
        return $this->hasMany(supplier_order::class);
        
    }
    public function suppliers_supplier_order()
    {
        return $this->belongsToMany(supplier::class, 'supplier_orders')
        ->withPivot('code', 'shipping_date', 'statut');
        
    }

    public function supplier_receipts()
    {
        return $this->hasMany(supplier_receipt::class);
        
    }
    public function suppliers_supplier_receipt()
    {
        return $this->belongsToMany(account_user::class, 'supplier_receipts')
        ->withPivot('code', 'statut');
        
    }
}
