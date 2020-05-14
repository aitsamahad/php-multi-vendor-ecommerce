<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store_Upload extends Model {

    protected $table = 'store_uploads'; // To explicitly set table name
    protected $fillable = [
        'store_id',
        'store_image'
    ];


}