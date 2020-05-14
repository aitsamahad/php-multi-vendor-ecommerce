<?php

use App\Controllers\BrandController;
use App\Middleware\AuthMiddlewareAdmin;


// $app->get('/add-product', DashboardController::class . ':getAddProduct')->setName('add.product');
// $app->post('/add-product', DashboardController::class . ':postAddProduct');

$app->group('/dashboard', function() {

    $this->get('/brand', BrandController::class . ':getAddBrand')->setName('brand');
    $this->post('/brand', BrandController::class . ':postAddBrand');
    $this->get('/brand/{brandid}', BrandController::class . ':getEditBrand')->setName('edit.brand');
    $this->post('/brand/{brandid}', BrandController::class . ':postEditBrand');
    $this->get('/brand/remove/{brandid}', BrandController::class . ':deleteBrand')->setName('delete.brand');

})->add(new AuthMiddlewareAdmin($container));