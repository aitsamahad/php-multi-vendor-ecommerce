<?php
 

namespace App\Middleware;

error_reporting(0);
ini_set('display_errors', 0);

class ValidationErrorsMiddleware extends Middleware {

    // PHP Magic Method
    public function __invoke($request, $response, $next) {
        
        $this->container->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
        unset($_SESSION['errors']);


        //Passing response of middleware in $next as current state of the $request and the current $response 
        $response = $next($request, $response);
        return $response;
    }

}