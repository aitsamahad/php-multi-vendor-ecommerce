<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Detail extends Model {

    protected $table = 'user_details'; // To explicitly set table name
    protected $fillable = [
        'user_id',
        'user_phone',
        'user_address',
        'user_city',
        'user_postcode',
        'user_state'
    ];


}