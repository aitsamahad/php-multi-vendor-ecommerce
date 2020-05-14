<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model {

    protected $table = 'reviews'; // To explicitly set table name
    protected $fillable = [
       'transaction_id',
       'user_id',
       'product_id',
       'review',
       'stars',
       'status'
    ];


}