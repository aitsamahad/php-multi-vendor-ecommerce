<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'products'; // To explicitly set table name
    protected $fillable = [
        'p_name',
        'p_description',
        'p_quantity',
        'p_seller',
        'p_category',
        'p_brand',
        'p_model',
        'p_warranty',
        'p_inside',
        'p_weight',
        'p_length'
    ];


}