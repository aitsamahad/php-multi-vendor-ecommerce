<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class EmailAvailable extends AbstractRule {

    //Email Exists checking method
    public function validate($input) {
        return User::where('u_email', $input)->count() === 0;
    }

}