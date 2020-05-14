<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reset_Request extends Model {

    protected $table = 'reset_requests'; // To explicitly set table name
    protected $fillable = [
       'user_id',
       'email'
    ];


}