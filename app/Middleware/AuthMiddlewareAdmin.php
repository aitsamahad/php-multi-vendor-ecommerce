<?php

namespace App\Middleware;

class AuthMiddlewareAdmin extends Middleware {


public function __invoke($request, $response, $next) {

    // Check if the user is not signed in
    if(!$this->container->Auth->checkAdmin()) {
        // Flash a message
        $this->container->flash->addMessage('Warning', 'Restricted Section, Please sign in');
        // Redirect un-authenticated user
        return $response->withRedirect($this->container->router->pathFor('signIn.page'));
    }

    $response = $next($request, $response);
    return $response;

}


}