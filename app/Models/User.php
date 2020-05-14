<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected $table = 'users'; // To explicitly set table name
    protected $fillable = [
        'u_name',
        'u_email',
        'u_password',
        'u_vendor'
    ];

    public function setPassword($password) {
        $this->update([
            'u_password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

}