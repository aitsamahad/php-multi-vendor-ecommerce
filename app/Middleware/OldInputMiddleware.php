<?php

namespace App\Middleware;

class OldInputMiddleware extends Middleware {

    // PHP Magic Method
    public function __invoke($request, $response, $next) {
        
        $this->container->view->getEnvironment()->addGlobal('old', $_SESSION['old']);
        $_SESSION['old'] = $request->getParams();

        //Passing response of middleware in $next as current state of the $request and the current $response 
        $response = $next($request, $response);
        return $response;
    }

}