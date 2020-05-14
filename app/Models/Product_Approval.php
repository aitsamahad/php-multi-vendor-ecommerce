<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_Approval extends Model {

    protected $table = 'product_approvals'; // To explicitly set table name
    protected $fillable = [
        'product_id',
        'store_id'
    ];


}