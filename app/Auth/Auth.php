<?php

namespace App\Auth;
use App\Models\User;

class Auth {
    
    public function user() {
        return User::where('u_id', $_SESSION['user'])->first();
    }

    public function check() {
        return isset($_SESSION['user']);
    }

    public function checkAdmin() {
        if (count(User::where([['u_id', '=', $_SESSION['user']], ['u_admin', '=', 1]])->get()) > 0){ 
            return true;
        } else {
            return false;
        }
    }
    
    public function attempt($email, $password) {

        //Grab user by email
        $user = User::where('u_email', $email)->first();
        
        //if !user return false
        if(!$user) {
            return false;
        }
        
        //verify password for that user
        if(password_verify($password, $user->u_password)) {
            
            //set into session
            $_SESSION['user'] = $user->u_id;

            return true;
        }
        return false;
    }

    public function logout() {
        unset($_SESSION['user']);
    }

}