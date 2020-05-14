<?php

use App\Controllers\ProductController;
use App\Controllers\Product_ApprovalController;
use App\Middleware\AuthMiddlewareAdmin;


// $app->get('/add-product', DashboardController::class . ':getAddProduct')->setName('add.product');
// $app->post('/add-product', DashboardController::class . ':postAddProduct');

$app->group('/dashboard', function() {

    // $this->get('/add-product', ProductController::class . ':getAddProduct')->setName('add.product');
    // $this->post('/add-product', ProductController::class . ':postAddProduct');
    // $this->get('/edit-product/{productid}', ProductController::class . ':getEditProduct')->setName('edit.product');
    // $this->post('/edit-product/{productid}', ProductController::class . ':postEditProduct');

    $this->get('/product/requests', Product_ApprovalController::class . ':getVendorRequests')->setName('product.requests');
    $this->get('/product/{productid}/{storeid}/approve', Product_ApprovalController::class . ':approveProductRequest')->setName('approve.product.request');
    $this->get('/product/{productid}/{storeid}/reject', Product_ApprovalController::class . ':rejectProductRequest')->setName('reject.product.request');

    $this->get('/vendor-products/{vendorid}/delete/{productid}', ProductController::class . ':deleteProduct')->setName('delete.product');

    $this->get('/vendor-products/{vendorid}', ProductController::class . ':getVendorProducts')->setName('vendor.products');

})->add(new AuthMiddlewareAdmin($container));