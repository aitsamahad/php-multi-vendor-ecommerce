<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\User_Detail;
use App\Models\Product;
use App\Models\Categories;
use App\Models\Upload;
use App\Models\Cart;
use Respect\Validation\Validator as validate;

class SearchController extends Controller {
    // public function searchByCategory ($request, $response, $args) {
    //     $categoryid = $args['categoryid'];
    //     $statement = $this->c->db->prepare("SELECT DISTINCT products.p_name, uploads.image, carts.quantity, (products.p_price * carts.quantity) AS total_price, products.p_id, carts.cart_id FROM products, uploads, carts, users WHERE products.p_id = carts.product_id AND uploads.product_id = products.p_id AND carts.user_id = ?");
    //     $statement->bind_param("s", $_SESSION['user']);
    //     $statement->execute();
    //     $statement->bind_result($p_name, $image, $p_quantity, $total_price, $p_id, $cart_id);
    //     $carts = array();
    //     while($statement->fetch()){
    //         $cart = array();
    //         $cart['p_name'] = $p_name;
    //         $cart['image'] = $image;
    //         $cart['p_quantity'] = $p_quantity;
    //         $cart['total_price'] = $total_price;
    //         $cart['p_id'] = $p_id;
    //         $cart['cart_id'] = $cart_id;
    
    //         array_push($carts, $cart);
    //     }

    //     $searchCat = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_category = ? AND uploads.product_id = products.p_id");
    //     $searchCat->bind_param("s", $categoryid);
    //     $searchCat->execute();
    //     $searchCat->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
    //     $CategoriesProducts = array();
    //     while($searchCat->fetch()){
    //         $product = array();
    //         $product['p_id'] = $p_id;
    //         $product['p_name'] = $p_name;
    //         $product['p_description'] = $p_description;
    //         $product['p_price'] = $p_price;
    //         $product['p_quantity'] = $p_quantity;
    //         $product['p_status'] = $p_status;
    //         $product['p_seller'] = $p_seller;
    //         $product['p_category'] = $p_category;
    //         $product['image'] = $image;
    //         $product['store_id'] = $store_id;
    //         array_push($CategoriesProducts, $product);
    //     }

    //     $cartSum = $this->c->db->prepare("SELECT SUM(total_price) AS sub_total FROM cart_items WHERE u_id = ?");
    //     $cartSum->bind_param("s", $_SESSION['user']);
    //     $cartSum->execute();
    //     $cartSum->bind_result($sub_total);
    //     $cartSumArray = array();
    //     while($cartSum->fetch()) {
    //         $cartArray = array();
    //         $cartArray['sub_total'] = $sub_total;
    //         array_push($cartSumArray, $cartArray);
    //     }

    //     $categories = Categories::get();
    //     $categoryName = Categories::where('c_id', $categoryid)->get();
    //     return $this->c->view->render($response, './search.twig', [
    //         'categories' => $categories,
    //         'cartDetails' => $carts,
    //         'cartSum' => $cartSumArray[0],
    //         'cartCount' => count($carts),
    //         'categoriesProducts' => $CategoriesProducts,
    //         'categoryname' => $categoryName[0]
    //     ]);
    // }

    public function searchByKeywordAndCategory ($request, $response, $args) {
        $keyword = $request->getParam('keyword');

        $statement = $this->c->db->prepare("SELECT DISTINCT products.p_name, uploads.image, carts.quantity, (products.p_price * carts.quantity) AS total_price, products.p_id, carts.cart_id FROM products, uploads, carts, users WHERE products.p_id = carts.product_id AND uploads.product_id = products.p_id AND carts.user_id = ?");
        $statement->bind_param("s", $_SESSION['user']);
        $statement->execute();
        $statement->bind_result($p_name, $image, $p_quantity, $total_price, $p_id, $cart_id);
        $carts = array();
        while($statement->fetch()){
            $cart = array();
            $cart['p_name'] = $p_name;
            $cart['image'] = $image;
            $cart['p_quantity'] = $p_quantity;
            $cart['total_price'] = $total_price;
            $cart['p_id'] = $p_id;
            $cart['cart_id'] = $cart_id;
    
            array_push($carts, $cart);
        }

        $searchCat = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_name LIKE '%" . $keyword . "%' AND products.p_description LIKE '%" . $keyword . "%' AND uploads.product_id = products.p_id");
        // $searchCat->bind_param("ss", $keyword, $keyword);
        $searchCat->execute();
        $searchCat->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
        $CategoriesProducts = array();
        while($searchCat->fetch()){
            $product = array();
            $product['p_id'] = $p_id;
            $product['p_name'] = $p_name;
            $product['p_description'] = $p_description;
            $product['p_price'] = $p_price;
            $product['p_quantity'] = $p_quantity;
            $product['p_status'] = $p_status;
            $product['p_seller'] = $p_seller;
            $product['p_category'] = $p_category;
            $product['image'] = $image;
            $product['store_id'] = $store_id;
            array_push($CategoriesProducts, $product);
        }

        $cartSum = $this->c->db->prepare("SELECT SUM(total_price) AS sub_total FROM cart_items WHERE u_id = ?");
        $cartSum->bind_param("s", $_SESSION['user']);
        $cartSum->execute();
        $cartSum->bind_result($sub_total);
        $cartSumArray = array();
        while($cartSum->fetch()) {
            $cartArray = array();
            $cartArray['sub_total'] = $sub_total;
            array_push($cartSumArray, $cartArray);
        }

        return $this->c->view->render($response, './search.twig', [
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'categoriesProducts' => $CategoriesProducts,
            'keyword' => $keyword
        ]);

        
    }

    public function genericSearch($reques, $response) {

    }
}