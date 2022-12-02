<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\region;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'firstname',
        'lastname',
        'status',
        'cin',
        'birthday',
        'photo',
        'photo_dir'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function accounts()
    {
        return $this->belongsToMany(account::class, 'account_user');

    }
    public function account_user()
    {
        return $this->hasMany(account_user::class);

    }
    public function phones()
    {
        return $this->morphToMany(phone::class, 'phoneable');
    }
    public function images()
    {
        return $this->morphToMany(image::class, 'imageable');
    }

    public function addresses()
    {
        return $this->morphToMany(addresse::class, 'addressable');
    }

    public function supplier_order_product_sizes()
    {
        return $this->hasMany(supplier_order_product_size::class);
        
    }

    public function product_sizes()
    {
        return $this->belongsToMany(product_size::class, 'supplier_order_product_size');
        
    }

    public function supplier_orders()
    {
        return $this->belongsToMany(supplier_order::class, 'supplier_order_product_size');
        
    }

    public function supplier_receipts()
    {
        return $this->belongsToMany(supplier_receipt::class, 'supplier_order_product_size');
        
    }

}
