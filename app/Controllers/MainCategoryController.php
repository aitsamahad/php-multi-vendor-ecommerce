<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Upload;
use Respect\Validation\Validator as validate;

class MainCategoryController extends Controller {

    public function getCategorySitePage($request, $response, $args) {
        $categoryid = $args['categoryid'];
        $singleCategory = Categories::where('c_id', $categoryid)->get();
        $categories = Categories::get();
        $brands = Brand::get();

        if ($_SESSION['user']) {

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

        $statement = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_category = ? AND uploads.product_id = products.p_id");
        $statement->bind_param("s", $categoryid);
        $statement->execute();
        $statement->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
        $products = array();
        while($statement->fetch()){
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
            array_push($products, $product);
        }

        return $this->c->view->render($response, './category.twig', [
            'categories' => $categories,
            'brands' => $brands,
            'products' => $products,
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'singleCategory' => $singleCategory[0]

        ]);

        } else {

            $statement = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_category = ? AND uploads.product_id = products.p_id");
            $statement->bind_param("s", $categoryid);
            $statement->execute();
            $statement->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
            $products = array();
            while($statement->fetch()){
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
                array_push($products, $product);
            }

            return $this->c->view->render($response, './category.twig', [
                'products' => $products,
                'brands' => $brands,
                'categories' => $categories,
                'singleCategory' => $singleCategory[0]
            ]);
        }
        
    }

    public function getBrandSitePage($request, $response, $args) {
        $brandid = $args['brandid'];
        $singleBrand = Brand::where('b_id', $categoryid)->get();
        $brands = Brand::get();
        $categories = Categories::get();

        if ($_SESSION['user']) {

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

        $statement = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_brand = ? AND uploads.product_id = products.p_id");
        $statement->bind_param("s", $brandid);
        $statement->execute();
        $statement->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_brand, $image, $store_id);
        $products = array();
        while($statement->fetch()){
            $product = array();
            $product['p_id'] = $p_id;
            $product['p_name'] = $p_name;
            $product['p_description'] = $p_description;
            $product['p_price'] = $p_price;
            $product['p_quantity'] = $p_quantity;
            $product['p_status'] = $p_status;
            $product['p_seller'] = $p_seller;
            $product['p_brand'] = $p_brand;
            $product['image'] = $image;
            $product['store_id'] = $store_id;
            array_push($products, $product);
        }

        return $this->c->view->render($response, './brand.twig', [
            'categories' => $categories,
            'brands' => $brands,
            'products' => $products,
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'singleBrand' => $singleBrand[0]

        ]);

        } else {

            $statement = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_brand = ? AND uploads.product_id = products.p_id");
            $statement->bind_param("s", $brandid);
            $statement->execute();
            $statement->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_brand, $image, $store_id);
            $products = array();
            while($statement->fetch()){
                $product = array();
                $product['p_id'] = $p_id;
                $product['p_name'] = $p_name;
                $product['p_description'] = $p_description;
                $product['p_price'] = $p_price;
                $product['p_quantity'] = $p_quantity;
                $product['p_status'] = $p_status;
                $product['p_seller'] = $p_seller;
                $product['p_brand'] = $p_brand;
                $product['image'] = $image;
                $product['store_id'] = $store_id;
                array_push($products, $product);
            }

            return $this->c->view->render($response, './brand.twig', [
                'products' => $products,
                'categories' => $categories,
                'brands' => $brands,
                'singleBrand' => $singleBrand[0]
            ]);
        }
        
    }


}