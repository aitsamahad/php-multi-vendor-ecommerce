<?php

use App\Controllers\MainCategoryController;


// $app->get('/add-product', DashboardController::class . ':getAddProduct')->setName('add.product');
// $app->post('/add-product', DashboardController::class . ':postAddProduct');

$app->group('', function() {
    $this->get('/category/{categoryid}', MainCategoryController::class . ':getCategorySitePage')->setName('site.category');
    $this->get('/brand/{brandid}', MainCategoryController::class . ':getBrandSitePage')->setName('site.brand');

});