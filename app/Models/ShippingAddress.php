<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
   protected $table = 'shipping_addresses';

    protected $fillable = [
        'user_id',
        'title',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'country_id',
        'state_id',
        'city',
        'zip_code',
        'address_type',
    ];
}
