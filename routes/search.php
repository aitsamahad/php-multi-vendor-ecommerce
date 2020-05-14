<?php

use App\Controllers\SearchController;


// $app->get('/add-product', DashboardController::class . ':getAddProduct')->setName('add.product');
// $app->post('/add-product', DashboardController::class . ':postAddProduct');

$app->group('/search', function() {

    $this->get('/', SearchController::class . ':searchByKeywordAndCategory')->setName('search.by.keyword');
    // $this->get('/generic/{keyword}', SearchController::class . ':searchByKeywordAndCategory')->setName('search.by.keyword.category');
    // $this->get('/{categoryid}', SearchController::class . ':searchByCategory')->setName('search.by.category');

});