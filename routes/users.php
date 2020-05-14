<?php

use App\Models\User;
use App\Controllers\UserController;
use App\Controllers\MainController;
use App\Middleware\AuthMiddlewareAdmin;



$app->group('/dashboard', function() {

    $this->get('/users', UserController::class . ':getAllUsers')->setName('dashboard.users');
    $this->get('/users/{u_id}', UserController::class . ':removeUser')->setName('dashboard.users.delete');

    $this->get('/user/add', UserController::class . ':getAddUser')->setName('dashboard.users.add');
    $this->post('/user/add', UserController::class . ':postAddUser');
})->add(new AuthMiddlewareAdmin($container));