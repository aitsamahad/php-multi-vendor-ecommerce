<?php

namespace App\Middleware;

class GuestMiddleware extends Middleware {


public function __invoke($request, $response, $next) {

    if ($this->container->Auth->check()) {
        return $response->withRedirect($this->container->router->pathFor('main.page'));
    }

    $response = $next($request, $response);
    return $response;

}


}