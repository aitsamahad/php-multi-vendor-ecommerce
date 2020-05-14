<?php

use App\Controllers\QuestionController;
use App\Middleware\AuthMiddleware;



$app->group('/question', function () {

    $this->post('/{sellerid}/{productid}', QuestionController::class . ':postQuestion')->setName('post.question');

})->add(new AuthMiddleware($container));
