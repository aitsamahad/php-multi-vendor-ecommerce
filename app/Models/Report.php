<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model {

    protected $table = 'reports'; // To explicitly set table name
    protected $fillable = [
        'r_reporter_u_id',
        'r_reported_u_id',
        'r_reported_reason',
    ];


}