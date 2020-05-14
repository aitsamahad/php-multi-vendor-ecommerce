<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\User_Detail;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Review;
use App\Models\Report;
use App\Models\Order;
use App\Models\Categories;
use App\Models\Upload;
use App\Models\Cart;
use Respect\Validation\Validator as validate;

class ReportController extends Controller {

    public function getReportsPage ($request, $response) {


        $REPORTS = $this->c->eloquent::table('reports')
        ->join('stores', 'reports.r_reported_u_id', '=', 'stores.s_store_vendor_id')
        ->join('users', 'reports.r_reporter_u_id', '=', 'users.u_id')
        ->where('reports.r_report_status', 0)
        ->select('reports.r_id', 'stores.s_store_name AS storeName', 'users.u_name AS reporterName', 'reports.r_reported_reason')
        ->get();

        return $this->c->view->render($response, './dashboard/reports.twig', [
            'reports' => $REPORTS
        ]);
    }

    public function markReportResolved ($request, $response, $args) {
        $REPORT_ID = $args['reportid'];

        $RESOLVED = Report::where('r_id', $REPORT_ID)->update([
            'r_report_status' => 1
        ]);

        return $response->withRedirect($this->c->router->pathFor('reports.page'));
    }
}