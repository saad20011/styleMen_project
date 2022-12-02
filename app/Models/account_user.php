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
    public function categories()
    {
        return $this->hasMany(categorie::class);
        
    }
    public function suppliers_supplier_receipt()
    {
        return $this->belongsToMany(account_user::class, 'supplier_receipts')
        ->withPivot('code', 'statut');
        
    }

    public function orders(){
        return $this->hasMany(order::class);
    }

    public function customer_order(){
        return $this->belongsToMany(customer::class, 'orders');
    }
    public function account_city_order(){
        return $this->belongsToMany(account_city::class, 'orders');
    }
    public function payment_type_order(){
        return $this->belongsToMany(payment_type::class, 'orders');
    }
    public function payment_method_order(){
        return $this->belongsToMany(payment_method::class, 'orders');
    }
    public function brand_source_order(){
        return $this->belongsToMany(brand_source::class, 'orders');
    }
    public function pickup_order(){
        return $this->belongsToMany(pickup::class, 'orders');
    }
    public function statuses_order(){
        return $this->belongsToMany(status::class, 'orders');
    }

    public function payment_commision_order(){
        return $this->belongsToMany(payment_commission::class, 'orders');
    }

    public function invoice_order(){
        return $this->belongsToMany(invoice::class, 'orders');
    }

    public function order_products(){
        return $this->hasMany(order_product::class);
    }

    public function order_order_product(){
        return $this->belongsToMany(order::class, 'order_products');
    }
    public function product_size_order_product(){
        return $this->belongsToMany(product_size::class, 'order_products');
    }
    public function offer_order_product(){
        return $this->belongsToMany(offer::class, 'order_products');
    }

    public function order_comments(){
        return $this->hasMany(order_comment::class);
    }

    public function order_order_comment(){
        return $this->belongsToMany(order::class, 'order_comments');
    }
    public function subcomment_order_comment(){
        return $this->belongsToMany(subcomment::class, 'order_comments');
    }
    public function status_order_comment(){
        return $this->belongsToMany(status::class, 'order_comments');
    }

}
