<?php

use App\Controllers\MainController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;


$app->get('/', MainController::class . ':getMainPage')->setName('main.page');
$app->get('/product/{productid}', MainController::class . ':getProductPage')->setName('product.page');
$app->get('/logout', MainController::class . ':logout')->setName('main.logout');



$app->group('/{productid}', function () {

    $this->get('/add/wishlist', MainController::class . ':addToWishList')->setName('add.to.wishlist');

})->add(new AuthMiddleware($container));


$app->group('', function () {

    $this->get('/register', MainController::class . ':getRegisterPage')->setName('register.page');
    $this->post('/register', MainController::class . ':postSignUp');

    $this->get('/reset', MainController::class . ':reset')->setName('main.reset');
    $this->post('/reset', MainController::class . ':postReset');

    $this->get('/reset-token', MainController::class . ':getResetToken')->setName('main.reset.token');
    $this->post('/reset-token', MainController::class . ':postResetToken');

    $this->get('/login', MainController::class . ':getSignInPage')->setName('signIn.page');
    $this->post('/login', MainController::class . ':postSignIn');
})->add(new GuestMiddleware($container));

$app->group('/checkout', function () {
    $this->get('', MainController::class . ':getCheckoutPage')->setName('checkout.page');
    $this->post('', MainController::class . ':postCheckoutPage');

    $this->get('/confirmed', MainController::class . ':getThankyouPage')->setName('thankyou.page');
    $this->get('/{orderid}/order-email', MainController::class . ':sendOrderEmail')->setName('order.mail');

})->add(new AuthMiddleware($container));
