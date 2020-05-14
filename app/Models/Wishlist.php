<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model {

    protected $table = 'wishlists'; // To explicitly set table name
    protected $fillable = [
       'w_product_id',
       'w_wisher_u_id'
    ];


}