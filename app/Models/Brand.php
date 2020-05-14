<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model {

    protected $table = 'brands'; // To explicitly set table name
    protected $fillable = [
       'b_name',
       'b_description'
    ];


}