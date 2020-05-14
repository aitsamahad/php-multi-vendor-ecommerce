<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store_Approval extends Model {

    protected $table = 'store_approvals'; // To explicitly set table name
    protected $fillable = [
        'store_id',
        'vendor_id'
    ];


}