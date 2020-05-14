<?php

use App\Controllers\AccountController;
use App\Middleware\AuthMiddleware;



$app->group('/account', function () {
    $this->get('/orders', AccountController::class . ':getOrderPage')->setName('order.page');
    $this->get('/orders/{id}', AccountController::class . ':getOrderDetailPage')->setName('order.detail.page');

    $this->get('/reports/{productid}', AccountController::class . ':getReportingPage')->setName('reporting.page');
    $this->get('/reported', AccountController::class . ':getReportedPage')->setName('reported.page');
    $this->post('/reports/{reporterID}/{reportedID}', AccountController::class . ':postReportingPage')->setName('post.reporting.page');

    $this->get('/reviews', AccountController::class . ':getReviewsPage')->setName('reviews.page');
    $this->get('/reviews/{id}', AccountController::class . ':getReviewDetailPage')->setName('review.detail.page');
    $this->post('/reviews/{id}', AccountController::class . ':postReviewDetailPage');

    $this->get('/wishlist', AccountController::class . ':getWishlist')->setName('account.wishlist.page');
    $this->get('/wishlist/{w_id}', AccountController::class . ':removeFromWishlist')->setName('remove.from.wishlist');

    $this->get('/change-password', AccountController::class . ':getChangePassword')->setName('change.password');
    $this->post('/change-password', AccountController::class . ':postChangePassword');

    $this->get('/addresses', AccountController::class . ':getAddresses')->setName('account.addresses');
    $this->get('/addresses-edit', AccountController::class . ':getAddressEdit')->setName('account.addresses.edit');
    $this->post('/addresses-edit', AccountController::class . ':postAddressEdit');

})->add(new AuthMiddleware($container));
