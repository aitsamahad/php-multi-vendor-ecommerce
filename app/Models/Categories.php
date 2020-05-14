<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model {

    protected $table = 'categories'; // To explicitly set table name
    protected $fillable = [
       'c_name'
    ];


}