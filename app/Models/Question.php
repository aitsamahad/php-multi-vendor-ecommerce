<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model {

    protected $table = 'questions'; // To explicitly set table name
    protected $fillable = [
       'q_question_u_id',
       'q_seller_id',
       'q_product_id',
       'question',
       'q_question_status'
    ];


}