<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $table = 'orders'; // To explicitly set table name
    protected $fillable = [
       'user_id',
       'payment_method',
       'address',
       'order_note',
       'total_price'
    ];


}