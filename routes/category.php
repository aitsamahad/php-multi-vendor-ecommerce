<?php

use App\Controllers\CategoryController;
use App\Middleware\AuthMiddlewareAdmin;

// $app->get('/add-product', DashboardController::class . ':getAddProduct')->setName('add.product');
// $app->post('/add-product', DashboardController::class . ':postAddProduct');

$app->group('/dashboard', function() {

    $this->get('', CategoryController::class . ':getDashboardUsers')->setName('dashboard.users.main');
    $this->get('/category', CategoryController::class . ':getAddCategory')->setName('category');
    $this->post('/category', CategoryController::class . ':postAddCategory');
    $this->get('/category/{categoryid}', CategoryController::class . ':getEditCategory')->setName('edit.category');
    $this->post('/category/{categoryid}', CategoryController::class . ':postEditCategory');
    $this->get('/category/remove/{categoryid}', CategoryController::class . ':deleteCategory')->setName('delete.category');

})->add(new AuthMiddlewareAdmin($container));