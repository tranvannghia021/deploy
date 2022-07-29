<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table='customers';
    protected $fillable=[
        'id_cus_shopify',
        'id_shops',
        'first_name',
        'last_name',
        'country',
        'phone',
        'email',
        'total_order',
        'total_spent',
        'cus_created_at',
        'created_at',
        'updated_at'
    ];
}
