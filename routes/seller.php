<?php

use App\Controllers\SellerController;
use App\Middleware\AuthMiddleware;



$app->group('/seller', function () {

    $this->get('/dashboard', SellerController::class . ':getDashboard')->setName('seller.dashboard');
    $this->get('', SellerController::class . ':getSellerSection')->setName('seller.section');
    $this->get('/apply', SellerController::class . ':getBecomeSeller')->setName('become.seller');
    $this->post('/apply', SellerController::class . ':postBecomeSeller');

    $this->get('/products', SellerController::class . ':getSellerProducts')->setName('seller.products');
    $this->get('/add-product', SellerController::class . ':getAddProduct')->setName('seller.add.product');
    $this->get('/product/{productid}/delete', SellerController::class . ':deleteSellerProduct')->setName('delete.seller.product');

    $this->post('/add-product', SellerController::class . ':postAddProduct');
    $this->get('/{productid}/product-details', SellerController::class . ':getAddDetails')->setName('seller.add.details');
    $this->post('/{productid}/product-details', SellerController::class . ':postAddDetails');

    $this->get('/product/{productid}', SellerController::class . ':getEditProductPage')->setName('edit.product.page');
    $this->post('/product/{productid}', SellerController::class . ':postEditProductPage');
    $this->post('/product/{productid}/upload/{storeid}', SellerController::class . ':uploadProductImageEdit')->setName('edit.product.page.upload');

    $this->post('/{productid}/product-image', SellerController::class . ':uploadProductImage')->setName('upload.product.image');
    $this->post('/{productid}/{imageid}', SellerController::class . ':deleteUploadedImage')->setName('delete.uploaded.image');
    $this->post('/{productid}/{imageid}/delete', SellerController::class . ':deleteUploadedImageEdit')->setName('delete.uploaded.image.edit');

    $this->get('/profile', SellerController::class . ':getSellerProfile')->setName('seller.profile');
    $this->post('/profile/{storeid}', SellerController::class . ':postSellerProfile')->setName('post.seller.profile');
    $this->get('/profile/remove/{storeid}', SellerController::class . ':deleteSellerImage')->setName('delete.seller.image');


    $this->get('/orders', SellerController::class . ':getSellerOrders')->setName('seller.orders');
    $this->get('/orders/{t_id}', SellerController::class . ':markAsDelivered')->setName('seller.orders.delivered');
    $this->get('/orders/{t_id}/cancel', SellerController::class . ':markAsCancelled')->setName('seller.orders.cancelled');

    $this->get('/{productid}/questions', SellerController::class . ':getProductQuestions')->setName('product.questions');
    $this->get('/{questionid}/answer', SellerController::class . ':getProductAnswerPage')->setName('product.questions.answer');
    $this->post('/{productid}/{questionid}/answer', SellerController::class . ':postProductAnswerPage')->setName('product.questions.answer.post');

})->add(new AuthMiddleware($container));
