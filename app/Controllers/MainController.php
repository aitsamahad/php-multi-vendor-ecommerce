<?php

namespace App\Controllers;

use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\User_Detail;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Reset_Request;
use App\Models\Question;
use App\Models\Review;
use App\Models\Order;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Upload;
use App\Models\Cart;
use Respect\Validation\Validator as validate;

class MainController extends Controller {
    public function getMainPage ($request, $response) {


        if ($_SESSION['user']) {

            $cat1 = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_category = 1 AND products.p_status = 1 AND uploads.product_id = products.p_id");
            $cat1->execute();
            $cat1->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
            $firstCat = array();
            while($cat1->fetch()){
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
                array_push($firstCat, $product);
            }

            $cat2 = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_category = 2 AND products.p_status = 1 AND uploads.product_id = products.p_id");
            $cat2->execute();
            $cat2->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
            $secondCat = array();
            while($cat2->fetch()){
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
                array_push($secondCat, $product);
            }

            $cat3 = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_category = 3 AND products.p_status = 1 AND uploads.product_id = products.p_id");
            $cat3->execute();
            $cat3->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
            $thirdCat = array();
            while($cat3->fetch()){
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
                array_push($thirdCat, $product);
            }

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
            $products = Product::where('p_status', 1)->get();
            $productImages = Upload::get();
            $categories = Categories::get();
            $brands = Brand::get();


            return $this->c->view->render($response, './index.twig', [
                'products' => $products,
                'productImages' => $productImages,
                'categories' => $categories,
                'brands' => $brands,
                'cartDetails' => $carts,
                'cartSum' => $cartSumArray[0],
                'cartCount' => count($carts),
                'firstCat' => $firstCat,
                'secondCat' => $secondCat,
                'thirdCat' => $thirdCat
            ]);

        } else {
            $cat1 = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_category = 1 AND products.p_status = 1 AND uploads.product_id = products.p_id");
            $cat1->execute();
            $cat1->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
            $firstCat = array();
            while($cat1->fetch()){
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
                array_push($firstCat, $product);
            }

            $cat2 = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_category = 2 AND products.p_status = 1 AND uploads.product_id = products.p_id");
            $cat2->execute();
            $cat2->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
            $secondCat = array();
            while($cat2->fetch()){
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
                array_push($secondCat, $product);
            }

            $cat3 = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_category = 3 AND products.p_status = 1 AND uploads.product_id = products.p_id");
            $cat3->execute();
            $cat3->bind_result($p_id, $p_name, $p_description, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
            $thirdCat = array();
            while($cat3->fetch()){
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
                array_push($thirdCat, $product);
            }
            $products = Product::where('p_status', 1)->get();
            $productImages = Upload::get();
            $categories = Categories::get();
            $brands = Brand::get();
        
            return $this->c->view->render($response, './index.twig', [
                'products' => $products,
                'productImages' => $productImages,
                'categories' => $categories,
                'brands' => $brands,
                'firstCat' => $firstCat,
                'secondCat' => $secondCat,
                'thirdCat' => $thirdCat
            ]);
        }
    }

    public function getRegisterPage($request, $response) {
        return $this->c->view->render($response, './register.twig');
    }

    public function postSignUp($request, $response) {
        // Submit the SignUp Form
        // Using Validator
        $validation = $this->c->validator->validate($request, [
            'u_name' => validate::notEmpty()->length(3, 100)->alpha(),
            'u_email' => validate::noWhitespace()->notEmpty()->email()->emailAvailable(),
            'u_password' => validate::noWhitespace()->notEmpty()
            // 'u_password' => validate::noWhitespace()->alnum()->length(8, 20)->notEmpty()
        ]);
        

        if ($validation->failed()){
            // Redirect back!
            $this->c->flash->addMessage('Danger', 'Something went wrong, please check all of the fields below!');
            return $response->withRedirect($this->c->router->pathFor('register.page'));
        }

       //Using Model
       $user = User::create([
           'u_name' => $request->getParam('u_name'),
           'u_email' => $request->getParam('u_email'),
           'u_password' => password_hash($request->getParam('u_password'), PASSWORD_DEFAULT)
       ]);

       $user_detail = User_Detail::create([
           'user_id' => $user->id,
           'user_phone' => $request->getParam('user_phone'),
           'user_address' => $request->getParam('user_address'),
           'user_city' => $request->getParam('user_city'),
           'user_postcode' => $request->getParam('user_postcode'),
           'user_state' => $request->getParam('user_state')
       ]);

       $this->c->mailer->sendMessage('./welcome.html.twig', ['user' => $user], function($message) use($user) {    
    
            $message->setTo($user->u_email, $user->u_name);
            $message->setSubject('Welcom to VirtualDukan Family!');
        });

       // Sending flash message after signing up
       $this->c->flash->addMessage('Success', 'You are now Registered!');

    //    After signup, sign in automatically
       $this->c->Auth->attempt($user->u_email, $request->getParam('u_password'));

        // To redirect to the Home page which we set in routes using setName
    return $response->withRedirect($this->c->router->pathFor('main.page'));
    }

    public function getSignInPage($request, $response) {
        return $this->c->view->render($response, './login.twig');  
    }

    public function postSignIn($request, $response) {
        $auth = $this->c->Auth->attempt(
            $request->getParam('email'),
            $request->getParam('password')
        );
        if(!$auth) {
            $this->c->flash->addMessage('Danger', 'Invalid credentials!');
            return $response->withRedirect($this->c->router->pathFor('signIn.page'));
        }

        return $response->withRedirect($this->c->router->pathFor('main.page'));
    }

    public function getProductPage($request, $response, $args) {
        
        $productid = $args['productid'];

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

        $statement = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_model, products.p_warranty, products.p_inside, products.p_weight, products.p_length, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_id = ? AND uploads.product_id = ?");
        $statement->bind_param("ss", $productid, $productid);
        $statement->execute();
        $statement->bind_result($p_id, $p_name, $p_description, $p_model, $p_warranty, $p_inside, $p_weight, $p_length, $p_price, $p_quantity, $p_status, $p_seller, $p_category, $image, $store_id);
        $products = array();
        while($statement->fetch()){
            $product = array();
            $product['p_id'] = $p_id;
            $product['p_name'] = $p_name;
            $product['p_description'] = $p_description;
            $product['p_model'] = $p_model;
            $product['p_warranty'] = $p_warranty;
            $product['p_inside'] = $p_inside;
            $product['p_weight'] = $p_weight;
            $product['p_length'] = $p_length;
            $product['p_price'] = $p_price;
            $product['p_quantity'] = $p_quantity;
            $product['p_status'] = $p_status;
            $product['p_seller'] = $p_seller;
            $product['p_category'] = $p_category;
            $product['image'] = $image;
            $product['store_id'] = $store_id;
            array_push($products, $product);
        }


        $reviews = $this->c->eloquent::table('productpage_reviews')->where([['product_id', '=', $productid]])->get();


        $questions = Question::where('q_product_id', $productid)->get();
        $questionCount = count($questions);
        $storeName = $this->c->eloquent::table('stores')
        ->join('products', 'products.p_seller', '=', 'stores.s_store_vendor_id')
        ->join('brands', 'brands.b_id', '=', 'products.p_category')
        ->where('products.p_id', '=', $productid)
        ->select('stores.s_store_name AS storeName', 'stores.s_store_vendor_id AS vendorId', 'brands.b_name AS brandName')
        ->first();


        return $this->c->view->render($response, './product.twig', [
            'products' => $products,
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'questions' => $questions,
            'questionCount' => $questionCount,
            'reviews' => $reviews,
            'reviewsCount' => count($reviews),
            'storeName' => $storeName

        ]);

        } else {

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

        $statement = $this->c->db->prepare("SELECT products.p_id, products.p_name, products.p_description, products.p_price, products.p_quantity, products.p_status, products.p_seller, products.p_category, uploads.image, uploads.store_id FROM products, uploads WHERE products.p_id = ? AND uploads.product_id = ?");
        $statement->bind_param("ss", $productid, $productid);
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


        $reviews = $this->c->eloquent::table('productpage_reviews')->where([['product_id', '=', $productid]])->get();
        // $products =  Product::get();
        // $productImages = Upload::get();

        $questions = Question::where('q_product_id', $productid)->get();
        $questionCount = count($questions);
        $storeName = $this->c->eloquent::table('stores')
        ->join('products', 'products.p_seller', '=', 'stores.s_store_vendor_id')
        ->where('products.p_id', '=', $productid)
        ->select('stores.s_store_name AS storeName', 'stores.s_store_vendor_id AS vendorId')
        ->first();

        return $this->c->view->render($response, './product.twig', [
            'products' => $products,
            'user' => $_SESSION['user'],
            'questions' => $questions,
            'questionCount' => $questionCount,
            'reviews' => $reviews,
            'reviewsCount' => count($reviews),
            'storeName' => $storeName
        ]);
    }
    }

    public function getCheckoutPage($request, $response) {

        $statement = $this->c->db->prepare("SELECT DISTINCT products.p_name, uploads.image, carts.quantity, products.p_price, (products.p_price * carts.quantity) AS total_price, products.p_id, carts.cart_id FROM products, uploads, carts, users WHERE products.p_id = carts.product_id AND uploads.product_id = products.p_id AND carts.user_id = ?");
            $statement->bind_param("s", $_SESSION['user']);
            $statement->execute();
            $statement->bind_result($p_name, $image, $p_quantity, $p_price, $total_price, $p_id, $cart_id);
            $carts = array();
            while($statement->fetch()){
                $cart = array();
                $cart['p_name'] = $p_name;
                $cart['image'] = $image;
                $cart['p_quantity'] = $p_quantity;
                $cart['p_price'] = $p_price;
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

            $userDetails = User_Detail::where('user_id', $_SESSION['user'])->get();

        return $this->c->view->render($response, './checkout.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'userDetails' => $userDetails[0]
        ]);
    }

    public function postCheckoutPage($request, $response, $args) {
            
        if (count(Cart::where('user_id', $_SESSION['user'])->get()) > 0) {           
            // Getting user billing details
            $userDetails = User_Detail::where('user_id', $_SESSION['user'])->get();

            // Adding order details to order table
            $cartSum = $this->c->db->prepare("SELECT SUM(total_price) AS sub_total FROM cart_items WHERE u_id = ?");
            $cartSum->bind_param("s", $_SESSION['user']);
            $cartSum->execute();
            $cartSum->bind_result($sub_total);
            $cartSumArray = array();
            while($cartSum->fetch()) {
                $order = Order::create([
                    'user_id' => (int) $_SESSION['user'],
                    'payment_method' => (int) $request->getParam('payment_method'),
                    'address' => $userDetails[0]->user_address . ', ' . $userDetails[0]->user_city . ', ' . $userDetails[0]->user_postcode . ', ' . $userDetails[0]->user_state,
                    'order_note' => $request->getParam('comments'),
                    'total_price' => $sub_total
                ]);
            }

            // Adding specific product in transaction table
            $statement = $this->c->db->prepare("SELECT DISTINCT products.p_seller, products.p_name, carts.quantity, products.p_price, (products.p_price * carts.quantity) AS total_price, products.p_id, carts.cart_id FROM products, carts, users WHERE products.p_id = carts.product_id AND carts.user_id = ?");
            $statement->bind_param("s", $_SESSION['user']);
            $statement->execute();
            $statement->bind_result($p_seller, $p_name, $p_quantity, $p_price, $total_price, $p_id, $cart_id);

            while($statement->fetch()){
                $transaction = Transaction::create([
                    't_quantity' => $p_quantity,
                    't_buyer' => (int) $_SESSION['user'],
                    't_seller' => $p_seller,
                    't_product_id' => $p_id,
                    't_price' => $p_price,
                    't_subtotal' => $total_price,
                    'order_id' => $order->id
                ]);
            }

            // Empty Cart
            $emptyCart = Cart::where('user_id', $_SESSION['user'])->delete([
                'user_id' => $_SESSION['user']
            ]);

            $ORDER_DETAILS = $this->c->eloquent::table('transactions')
                ->join('orders', 'orders.order_id', '=', 'transactions.order_id')
                ->join('payment_methods', 'payment_methods.payment_id', '=', 'orders.payment_method')
                ->join('products', 'products.p_id', '=', 'transactions.t_product_id')
                ->where('orders.order_id', '=', $order->id)
                ->select('orders.address', 'orders.order_note', 'orders.total_price', 'transactions.t_quantity', 'transactions.t_price', 'transactions.t_subtotal', 'products.p_id', 'products.p_name', 'transactions.t_product_id', 'payment_methods.payment_name')
                ->get();

            $user = User::where('u_id', $_SESSION['user'])->first();
            $userDetail = User_Detail::where('user_id', $user->u_id)->first();

            $this->c->mailer->sendMessage('./order-confirmed-mail.twig', ['user' => $user, 'orders' => $ORDER_DETAILS, 'userDetail' => $userDetail], function($message) use($user) {

                $message->setTo($user->u_email, $user->u_name);
                $message->setSubject('Order Confirmation [Virtual Dukan]');

            });

            return $response->withRedirect($this->c->router->pathFor('thankyou.page'));

        }

        return 'Cart is Empty';
    }

    public function getThankyouPage($request, $response) {
        return $this->c->view->render($response, './thankyou.twig');
    }

    public function logout($request, $response, $args) {
        $this->c->Auth->logout();

        return $response->withRedirect($this->c->router->pathFor('main.page'));
    }

    public function addToWishList ($request, $response, $args) {
        
        $productid = $args['productid'];

        if (count(Wishlist::where([['w_product_id', '=', $productid], ['w_wisher_u_id', '=', $_SESSION['user']]])->get()) > 0) {

            $this->c->flash->addMessage('Danger', 'Product is already in your wishlist');
            return $response->withRedirect($this->c->router->pathFor('product.page', ['productid' => $productid]));

        } else {

        $ADD_TO_WISHLIST = Wishlist::create([
            'w_product_id' => $productid,
            'w_wisher_u_id' => $_SESSION['user']
            ]);
            
            $this->c->flash->addMessage('Success', 'Added to your wishlist!');
            return $response->withRedirect($this->c->router->pathFor('product.page', ['productid' => $productid]));
        }
    }

    public function sendOrderEmail ($request, $response, $args) {

        $orderid = (int) $args['orderid'];


        $ORDER_DETAILS = $this->c->eloquent::table('transactions')
        ->join('orders', 'orders.order_id', '=', 'transactions.order_id')
        ->join('payment_methods', 'payment_methods.payment_id', '=', 'orders.payment_method')
        ->join('products', 'products.p_id', '=', 'transactions.t_product_id')
        ->where('orders.order_id', '=', $orderid)
        ->select('orders.address', 'orders.order_note', 'orders.total_price', 'transactions.t_quantity', 'transactions.t_price', 'transactions.t_subtotal', 'products.p_name', 'transactions.t_product_id', 'payment_methods.payment_name')
        ->get();

        var_dump($ORDER_DETAILS[0]->payment_name);
        die();

        $user = User::where('u_id', $_SESSION['user'])->first();

        $COMBINED_DATA = $user->u_email . $user->u_name;

        $VerificationToken = password_hash($COMBINED_DATA, PASSWORD_DEFAULT);

        // var_dump($UserUUID);
        // die();

        $this->c->mailer->sendMessage('./order-confirmed-mail.twig', ['orders' => $ORDER_DETAILS, 'bytes' => $VerificationToken], function($message) use($args) {
            $email = User::where('u_id', $_SESSION['user'])->first();
            $orderid = (int) $args['orderid'];



            // $ORDER_DETAILS = $this->c->eloquent::table('transactions')
            // ->join('orders', 'orders.order_id', '=', 'transactions.order_id')
            // ->where('orders.order_id', '=', 'users.u_id')
            // ->select('store_approvals.store_approval_id AS id', 'stores.s_store_name AS store_name', 'stores.s_id AS store_id', 'users.u_id', 'stores.s_store_address AS store_address', 'users.u_name AS u_name')
            // ->get();

            $message->setTo($email->u_email, $email->u_name);
            $message->setSubject('Welcome to the Team!');
        });
    }

    public function reset($request, $response, $args) {

        return $this->c->view->render($response, './reset.twig');
    }

    public function postReset($request, $response, $args) {
        $email = $request->getParam('email');

        $User = User::where('u_email', $email)->first();

        if(count(User::where('u_email', $email)->get()) < 1) return "Your are not registered";

        if (count(Reset_Request::where('email', $email)->get()) < 1) {

            $COMBINED_DATA = $User->u_email . $User->u_name;

            $VerificationToken = password_hash($COMBINED_DATA, PASSWORD_DEFAULT);

            $GENERATING_TOKEN = User::where('u_id', $User->u_id)->update([
                'u_verification_token' => $VerificationToken
            ]);

            $GENERATE_REQUEST = Reset_Request::create([
                'user_id' => $User->u_id,
                'email' => $email
            ]);

            $this->c->mailer->sendMessage('./password-reset.twig', ['email' => $email, 'token' => $VerificationToken, 'user' => $User], function($message) use($User) {    
    
                $message->setTo($User->u_email, $User->u_name);
                $message->setSubject('[Reset] Password for email:' . $User->u_email . '');
            });


            $this->c->flash->addMessage('Success', 'You will receive reset confirmation email with vefirication token on your registered email shortly.');
            return $response->withRedirect($this->c->router->pathFor('main.reset.token'));

        } else {

            $COMBINED_DATA = $User->u_email . $User->u_name;

            $VerificationToken = password_hash($COMBINED_DATA, PASSWORD_DEFAULT);

            $GENERATING_TOKEN = User::where('u_id', $User->u_id)->update([
                'u_verification_token' => $VerificationToken
            ]);

            $this->c->mailer->sendMessage('./password-reset.twig', ['email' => $email, 'token' => $VerificationToken, 'user' => $User], function($message) use($User) {    
    
                $message->setTo($User->u_email, $User->u_name);
                $message->setSubject('[Reset] Password for email:' . $User->u_email . '');
            });

            $this->c->flash->addMessage('Success', 'You will receive reset confirmation email with vefirication token on your registered email shortly.');
            return $response->withRedirect($this->c->router->pathFor('main.reset.token'));
        }
    }

    public function getResetToken($request, $response, $args) {

        return $this->c->view->render($response, './reset-token.twig');
    }

    public function postResetToken($request, $response, $args) {

        $token = $request->getParam('token');
        $email = $request->getParam('email');
        $password = $request->getParam('password');

        $HashPassword = password_hash($password, PASSWORD_DEFAULT);

        if(count(User::where('u_email', $email)->get()) < 1) return "Your are not registered";

        if (count(Reset_Request::where('email', $email)->get()) < 1) {
            
            $this->c->flash->addMessage('Danger', 'Your request has been expired.');
            return $response->withRedirect($this->c->router->pathFor('main.reset.token'));
        
        } else if (count(User::where('u_verification_token', $token)->get()) < 1) {

            $this->c->flash->addMessage('Danger', 'Invalid Token');
            return $response->withRedirect($this->c->router->pathFor('main.reset.token'));
        } else {
            Reset_Request::where('email', $email)->delete([
                'email' => $email
            ]);
            User::where('u_email', $email)->update([
                'u_password' => $HashPassword
            ]);

            $this->c->flash->addMessage('Success', 'Password Reset Success! You can now log in.');
            return $response->withRedirect($this->c->router->pathFor('signIn.page'));
        }

        return $this->c->view->render($response, './reset-token.twig');
    }


 }