<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Product_Approval;

class Product_ApprovalController extends Controller {

    public function getVendorRequests($request, $response) {

        // $vendor_requests = Store_Approval::get();
        $product_requests = $this->c->eloquent::table('product_approvals')
        ->join('stores', 'product_approvals.store_id', '=', 'stores.s_id')
        ->join('products', 'product_approvals.product_id', '=', 'products.p_id')
        ->select('product_approvals.product_approval_id AS id', 'stores.s_store_name AS store_name', 'stores.s_id AS store_id', 'products.p_id', 'products.p_name AS p_name', 'products.p_description AS p_description')
        ->get();


        return $this->c->view->render($response, './dashboard/product-approvals.twig', [
            'product_requests' => $product_requests
        ]);
    }

    public function approveProductRequest($request, $response, $args) {
        $store_id = $args['storeid'];
        $productid = $args['productid'];

        $request_approved = Product::where('p_id', $productid)->update([
            'p_status' => 1
        ]);


        $request_delete = Product_Approval::where('product_id', $productid)->delete();

        
        $this->c->flash->addMessage('Success', 'Product is now active!');

      
        return $response->withRedirect($this->c->router->pathFor('product.requests'));
    }

    public function rejectProductRequest($request, $response, $args) {
        $store_id = $args['storeid'];
        $productid = $args['productid'];


        $request_delete = Product_Approval::where('product_id', $productid)->delete();

        
        $this->c->flash->addMessage('Success', 'Product Request rejected!');

       
        return $response->withRedirect($this->c->router->pathFor('product.requests'));
    }

}