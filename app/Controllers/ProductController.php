<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Product_Approval;
use App\Models\Categories;
use App\Models\Store;
use App\Models\Brand;
use App\Models\Upload;
use Respect\Validation\Validator as validate;

class ProductController extends Controller {


    public function getVendorProducts($request, $response, $args) {
        $vendorid = $args['vendorid'];

        $categories = Categories::get();
        $productImages = Upload::where('store_id', $vendorid)->get();


        $products = Product::where('p_seller', $vendorid)->get();

        return $this->c->view->render($response, './dashboard/vendor-products.twig', [
            'vendorid' => $vendorid,
            'categories' => $categories,
            'products' => $products,
            'productImages' => $productImages
        ]);
    }

    public function deleteProduct($request, $response, $args) {
        $vendorid = $args['vendorid'];
        $productid = $args['productid'];

        $product = Product::where('p_id', $productid)->delete([
            'p_id' => $productid
        ]);

        $deleteImage = Upload::where('product_id', $productid)->delete([
            'product_id' => $productid
        ]);

        $this->c->flash->addMessage('Success', 'Vendor product successfully deleted!');

        return $response->withRedirect($this->c->router->pathFor('vendor.products', ['vendorid' => $vendorid]));
    }
    // PRODUCT SECTION END

}