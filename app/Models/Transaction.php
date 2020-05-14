<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    protected $table = 'transactions'; // To explicitly set table name
    protected $fillable = [
       't_quantity',
       't_buyer',
       't_seller',
       't_product_id',
       't_product_id',
       't_price',
       't_subtotal',
       'order_id'
    ];


}