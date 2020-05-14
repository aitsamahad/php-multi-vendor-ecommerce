<?php

use App\Controllers\VendorController;
use App\Middleware\AuthMiddlewareAdmin;

// $app->get('/add-product', DashboardController::class . ':getAddProduct')->setName('add.product');
// $app->post('/add-product', DashboardController::class . ':postAddProduct');

$app->group('/dashboard', function() {

    $this->get('/vendors', VendorController::class . ':getAllVendors')->setName('vendors');
    $this->get('/vendor/add', VendorController::class . ':getAddVendor')->setName('add.vendor');
    $this->post('/vendor/add', VendorController::class . ':postAddVendor');

    $this->get('/vendor/requests', VendorController::class . ':getVendorRequests')->setName('vendor.requests');
    $this->get('/vendor/{vendorid}/{storeid}/approve', VendorController::class . ':approveVendorRequest')->setName('approve.request');
    $this->get('/vendor/{vendorid}/{storeid}/reject', VendorController::class . ':rejectVendorRequest')->setName('reject.request');

    $this->get('/vendor/{storeid}', VendorController::class . ':getEditVendor')->setName('edit.vendor');
    $this->post('/vendor/{storeid}', VendorController::class . ':postEditVendor');
    $this->get('/vendor/remove/{storeid}', VendorController::class . ':deleteVendor')->setName('delete.vendor');

    $this->get('/vendor/image/{storeid}', VendorController::class . ':deleteVendorImage')->setName('delete.vendor.image');

    $this->get('/vendor/ban/{storeid}', VendorController::class . ':banVendor')->setName('ban.vendor');
    $this->get('/vendor/activate/{storeid}', VendorController::class . ':activateVendor')->setName('activate.vendor');

})->add(new AuthMiddlewareAdmin($container));