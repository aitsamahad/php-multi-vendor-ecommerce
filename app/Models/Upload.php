<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model {

    protected $table = 'uploads'; // To explicitly set table name
    protected $fillable = [
        'product_id',
        'image',
        'store_id'
    ];


}