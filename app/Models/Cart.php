<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model {

    protected $table = 'carts'; // To explicitly set table name
    protected $fillable = [
       'user_id',
       'product_id',
       'quantity'
    ];


}