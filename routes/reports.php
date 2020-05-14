<?php

use App\Controllers\ReportController;
use App\Middleware\AuthMiddlewareAdmin;



$app->group('/dashboard', function () {
    $this->get('/reports', ReportController::class . ':getReportsPage')->setName('reports.page');
    $this->get('/reports/{reportid}', ReportController::class . ':markReportResolved')->setName('resolve.report');

})->add(new AuthMiddlewareAdmin($container));
