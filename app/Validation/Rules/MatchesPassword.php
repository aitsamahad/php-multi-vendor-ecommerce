<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class MatchesPassword extends AbstractRule {

    protected $password;

    public function __construct($password) {
        $this->password = $password;
    }

    //Email Exists checking method
    public function validate($input) {

        //return User::where('u_email', $input)->count() === 0;

        return password_verify($input, $this->password);

    }

}