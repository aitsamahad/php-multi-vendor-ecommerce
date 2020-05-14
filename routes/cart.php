<?php

use App\Controllers\CartController;


// $app->get('/add-product', DashboardController::class . ':getAddProduct')->setName('add.product');
// $app->post('/add-product', DashboardController::class . ':postAddProduct');

$app->group('/cart', function() {

    $this->post('/add/{productid}', CartController::class . ':addToCart')->setName('add.to.cart');
    $this->get('/add/{productid}', CartController::class . ':addToCart');
    $this->get('/remove/{cartid}', CartController::class . ':removeFromCart')->setName('remove.from.cart');

});