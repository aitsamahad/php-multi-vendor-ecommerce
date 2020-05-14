<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Models\User_Detail;
use App\Controllers\Controller;
use Respect\Validation\Validator as validate;

class AuthController extends Controller {

    public function postSignUp($request, $response) {
        // Submit the SignUp Form

        // Using Validator
        $validation = $this->c->validator->validate($request, [
            'u_name' => validate::notEmpty()->length(3, 100)->alpha(),
            'u_email' => validate::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'u_password' => validate::noWhitespace()->alnum()->length(8, 20)->notEmpty()
        ]);
        
        function passwordConfirm($password, $confirmPassword) {
            var_dump($password);
            die();
            if ($password === $confirmPassword) {
                return true;
            } else {
                return false;
            }
        }

        $password_match = passwordConfirm($request->getParam('u_password'), $request->getParam('u_confirm'));

        if ($validation->failed() || $password_match){
            // Redirect back!
            $this->c->flash->addMessage('Danger', 'Something went wrong, please check all of the fields below!');
            return $response->withRedirect($this->c->router->pathFor('register.page'));
        }

       //Using Model
       $user = User::create([
           'u_name' => $request->getParam('u_name'),
           'u_email' => $request->getParam('u_email'),
           'u_password' => password_hash($request->getParam('u_password'), PASSWORD_DEFAULT)
       ]);

       $user_detail = User_Detail::create([
           'user_id' => $user->id,
           'user_phone' => $request->getParam('user_phone'),
           'user_address' => $request->getParam('user_address'),
           'user_city' => $request->getParam('user_city'),
           'user_postcode' => $request->getParam('user_postcode'),
           'user_state' => $request->getParam('user_state')
       ]);

       // Sending flash message after signing up
       $this->c->flash->addMessage('Success', 'You are now Registered!');

       // After signup, sign in automatically
    //    $this->c->Auth->attempt($user->u_email, $request->getParam('psw'));

        // To redirect to the Home page which we set in routes using setName
    return $response->withRedirect($this->c->router->pathFor('register.page'));
    }

}