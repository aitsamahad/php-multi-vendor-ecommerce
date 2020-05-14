<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\User_Detail;
use App\Models\Product;
use App\Models\Store;
use Respect\Validation\Validator as validate;

class UserController extends Controller {
    public function getAllUsers ($request, $response, $args) {
        $users = $this->c->eloquent::table('users')
        ->join('user_details', 'user_details.user_id', '=', 'users.u_id')
        ->where('users.u_admin', '!=', '1')
        ->get();

        return $this->c->view->render($response, './dashboard/users.twig', [
            'users' => $users
        ]);
    }

    public function removeUser($request, $response, $args) {
        $u_id = $args['u_id'];

        $REMOVE_USER = User::where('u_id', $u_id)->delete([
            'u_id' => $u_id
        ]);
        $REMOVE_USER_DETAILS = User_Detail::where('user_id', $u_id)->delete([
            'user_id' => $u_id
        ]);
        $REMOVE_PRODUCTS = Product::where('p_seller', $u_id)->delete([
            'p_seller' => $u_id
        ]);
        $REMOVE_STORE = Store::where('s_store_vendor_id', $u_id)->delete([
            's_store_vendor_id' => $u_id
        ]);

        $this->c->flash->addMessage('Success', 'User Deleted!');
        return $response->withRedirect($this->c->router->pathFor('dashboard.users'));
    }

    public function getAddUser($request, $response, $args) {

        return $this->c->view->render($response, './dashboard/user-add.twig');
    }

    public function postAddUser($request, $response, $args) {
                // Using Validator
                $validation = $this->c->validator->validate($request, [
                    'u_name' => validate::notEmpty()->length(3, 100)->alpha(),
                    'u_email' => validate::noWhitespace()->notEmpty()->email()->emailAvailable(),
                    'u_password' => validate::noWhitespace()->notEmpty()
                    // 'u_password' => validate::noWhitespace()->alnum()->length(8, 20)->notEmpty()
                ]);
                
        
                if ($validation->failed()){
                    // Redirect back!
                    $this->c->flash->addMessage('Danger', 'Something went wrong, please check all of the fields below!');
                    return $response->withRedirect($this->c->router->pathFor('dashboard.users.add'));
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
        
               $this->c->mailer->sendMessage('./welcome.html.twig', ['user' => $user], function($message) use($user) {    
            
                    $message->setTo($user->u_email, $user->u_name);
                    $message->setSubject('Welcom to VirtualDukan Family!');
                });
        
               // Sending flash message after signing up
               $this->c->flash->addMessage('Success', 'New User Registered!');
        
                // To redirect to the Home page which we set in routes using setName
            return $response->withRedirect($this->c->router->pathFor('dashboard.users'));
    }

}