<?php

namespace App\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator {
    
    protected $errors;

    // Validation function for validation input fields
    public function validate($request, array $rules) {
        
        // Validating if fields are filled with correct/valid data
        
        foreach ($rules as $field => $rule) {
            try {

            $rule->setName(ucfirst($field))->assert($request->getParam($field));
        
        } catch (NestedValidationException $e) {
            $this->errors[$field] = $e->getMessages();
        }
    }

    $_SESSION['errors'] = $this->errors;

    return $this;

    }

    public function failed() {

        return !empty($this->errors);
    }

}