<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model {

    protected $table = 'stores'; // To explicitly set table name
    protected $fillable = [
        's_store_name',
        's_store_vendor_id',
        's_store_address',
        's_store_status'
    ];


}