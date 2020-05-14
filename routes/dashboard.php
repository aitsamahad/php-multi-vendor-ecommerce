<?php

use App\Controllers\DashboardController;


$app->get('/add-product', DashboardController::class . ':getAddProduct')->setName('add.product');
$app->post('/add-product', DashboardController::class . ':postAddProduct');

$app->group('/dashboard', function() {

    $this->get('/add-product', DashboardController::class . ':getAddProduct')->setName('add.product');
    $this->post('/add-product', DashboardController::class . ':postAddProduct');
    $this->get('/edit-product/{productid}', DashboardController::class . ':getEditProduct')->setName('edit.product');
    $this->post('/edit-product/{productid}', DashboardController::class . ':postEditProduct');


    $this->get('/category', DashboardController::class . ':getAddCategory')->setName('category');
    $this->post('/category', DashboardController::class . ':postAddCategory');
    $this->get('/category/{categoryid}', DashboardController::class . ':getEditCategory')->setName('edit.category');
    $this->post('/category/{categoryid}', DashboardController::class . ':postEditCategory');
    $this->get('/category/remove/{categoryid}', DashboardController::class . ':deleteCategory')->setName('delete.category');


    $this->get('/brand', DashboardController::class . ':getAddBrand')->setName('brand');
    $this->post('/brand', DashboardController::class . ':postAddBrand');
    $this->get('/brand/{brandid}', DashboardController::class . ':getEditBrand')->setName('edit.brand');
    $this->post('/brand/{brandid}', DashboardController::class . ':postEditBrand');
    $this->get('/brand/remove/{brandid}', DashboardController::class . ':deleteBrand')->setName('delete.brand');

});