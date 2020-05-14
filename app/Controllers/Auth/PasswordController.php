<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\Controller;
use Respect\Validation\Validator as v;

class PasswordController extends Controller {

    public function getChangePassword($request, $response) {
        return $this->c->view->render($response, './change.twig');
    }

    public function postChangePassword($request, $response) {
        $validation = $this->c->validator->validate($request, [
            // password_old is the name of the field of password template
            // password is also the name of the field of password template
            'password_old' => v::noWhitespace()->notEmpty()->matchesPassword($this->c->Auth->user()->password),
            'password' => v::noWhitespace()->notEmpty()
        ]);

        if($validation->failed()) {
            return $response->withRedirect($this->c->router->pathFor('auth.password.change'));
        }

        // Setting the new password
        $this->c->Auth->user()->setPassword($request->getParam('password'));

        // Flash message
        $this->c->flash->addMessage('Global', 'Your password has been changed');

        // Redirecting after password change
        return $response->withRedirect($this->c->router->pathFor('home'));

    }
    
}