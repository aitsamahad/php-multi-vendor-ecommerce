<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Wishlist;
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

class AccountController extends Controller {
    public function getOrderPage($request, $response) {
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

            $orders = $this->c->db->prepare("SELECT products.p_id, products.p_name, uploads.image, orders.order_id, orders.payment_method, orders.address, orders.order_note, transactions.t_id, transactions.t_quantity, transactions.t_status, transactions.created_at, transactions.t_subtotal  from orders, transactions, uploads, products where orders.order_id = transactions.order_id AND products.p_id = transactions.t_product_id AND uploads.product_id = transactions.t_product_id AND transactions.t_buyer = ?");
            $orders->bind_param("s", $_SESSION['user']);
            $orders->execute();
            $orders->bind_result($p_id, $p_name, $image, $order_id, $payment_method, $address, $order_note, $t_id, $t_quantity, $t_status, $created_at, $t_subtotal);
            $ordersList = array();
            while($orders->fetch()) {
                $order = array();
                $order['p_id'] = $p_id;
                $order['p_name'] = $p_name;
                $order['image'] = $image;
                $order['order_id'] = $order_id;
                $order['payment_method'] = $payment_method;
                $order['address'] = $address;
                $order['order_note'] = $order_note;
                $order['t_id'] = $t_id;
                $order['t_quantity'] = $t_quantity;
                $order['t_status'] = $t_status;
                $order['created_at'] = $created_at;
                $order['t_subtotal'] = $t_subtotal;
                array_push($ordersList, $order);
            }

        return $this->c->view->render($response, './orders.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'orders' => $ordersList
        ]);
    }

    public function getOrderDetailPage($request, $response, $args) {
        $orderID = (int) $args['id'];

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

            $orders = $this->c->db->prepare("SELECT products.p_id, products.p_name, uploads.image, orders.order_id, payment_methods.payment_name, orders.address, orders.order_note, transactions.t_id, transactions.t_price, transactions.t_quantity, transactions.t_status, transactions.created_at, transactions.t_subtotal  from orders, transactions, uploads, products, payment_methods where orders.order_id = transactions.order_id AND products.p_id = transactions.t_product_id AND payment_methods.payment_id = orders.payment_method AND uploads.product_id = transactions.t_product_id AND transactions.t_buyer = ? AND transactions.t_id = ?");
            $orders->bind_param("ss", $_SESSION['user'], $orderID);
            $orders->execute();
            $orders->bind_result($p_id, $p_name, $image, $order_id, $payment_method, $address, $order_note, $t_id, $t_price, $t_quantity, $t_status, $created_at, $t_subtotal);
            $ordersList = array();
            while($orders->fetch()) {
                $order = array();
                $order['p_id'] = $p_id;
                $order['p_name'] = $p_name;
                $order['image'] = $image;
                $order['order_id'] = $order_id;
                $order['payment_method'] = $payment_method;
                $order['address'] = $address;
                $order['order_note'] = $order_note;
                $order['t_id'] = $t_id;
                $order['t_quantity'] = $t_quantity;
                $order['t_status'] = $t_status;
                $order['created_at'] = $created_at;
                $order['t_subtotal'] = $t_subtotal;
                $order['t_price'] = $t_price;
                array_push($ordersList, $order);
            }

            $username = User::select('u_name')->where('u_id', $_SESSION['user'])->get();

        return $this->c->view->render($response, './order-details.twig', [
            'user' => $_SESSION['user'],
            'username' => $username[0],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'order' => $ordersList[0]
        ]);
        
    }

    public function getReviewDetailPage ($request, $response, $args) {
        $orderID = (int) $args['id'];

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

        return $this->c->view->render($response, './review-details.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'id' => $orderID
        ]);
    }

    public function postReviewDetailPage ($request, $response, $args) {
        $id = (int) $args['id'];
        $review = $request->getParam('review');
        $stars = (int) $request->getParam('stars');


        // $statement = $this->c->db->prepare("UPDATE reviews SET review = ?, stars = ?, status = 1 WHERE transaction_id = ?");
        // $statement->bind_param("sss", $review, $stars, $transactionID);
        // $statement->execute();

        $postReview = Review::where('transaction_id', $id)->update([
            'review' => $review,
            'stars' => $stars,
            'status' => 1
        ]);

        return $response->withRedirect($this->c->router->pathFor('reviews.page'));
    }

    public function getReviewsPage ($request, $response) {

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

            $orders = $this->c->db->prepare("SELECT p_id, p_name, image, order_id, payment_method, address, order_note, t_id, t_quantity, review_status, t_status, created_at, t_subtotal FROM product_reviews WHERE t_buyer = ?");
            $orders->bind_param("s", $_SESSION['user']);
            $orders->execute();
            $orders->bind_result($p_id, $p_name, $image, $order_id, $payment_method, $address, $order_note, $t_id, $t_quantity, $review_status, $t_status, $created_at, $t_subtotal);
            $ordersList = array();
            while($orders->fetch()) {
                $order = array();
                $order['p_id'] = $p_id;
                $order['p_name'] = $p_name;
                $order['image'] = $image;
                $order['order_id'] = $order_id;
                $order['payment_method'] = $payment_method;
                $order['address'] = $address;
                $order['order_note'] = $order_note;
                $order['t_id'] = $t_id;
                $order['t_quantity'] = $t_quantity;
                $order['review_status'] = $review_status;
                $order['t_status'] = $t_status;
                $order['created_at'] = $created_at;
                $order['t_subtotal'] = $t_subtotal;
                array_push($ordersList, $order);
            }


        return $this->c->view->render($response, './reviews.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'orders' => $ordersList
        ]);
    }

    public function getReportingPage ($request, $response, $args) {

        $productid = $args['productid'];


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

            $storeName = $this->c->eloquent::table('stores')
            ->join('products', 'products.p_seller', '=', 'stores.s_store_vendor_id')
            ->where('products.p_id', '=', $productid)
            ->select('stores.s_store_name AS storeName', 'stores.s_store_vendor_id AS vendorId')
            ->first();

        return $this->c->view->render($response, './report.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'storeName' => $storeName
        ]);
    }

    public function postReportingPage ($request, $response, $args) {
        $reporterID = $args['reporterID'];
        $reportedID = $args['reportedID'];

        $POST_REPORT = Report::create([
            'r_reporter_u_id' => $reporterID,
            'r_reported_u_id' => $reportedID,
            'r_reported_reason' => $request->getParam('report_reason'),
        ]);

        return $response->withRedirect($this->c->router->pathFor('reported.page'));

    }

    public function getReportedPage ($request, $response) {
        return $this->c->view->render($response, './reported.twig');
    }

    public function getWishlist ($request, $response, $args) {

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

        $Wishlist = $this->c->eloquent::table('wishlists')
        ->join('products', 'products.p_id', '=', 'wishlists.w_product_id')
        ->join('uploads', 'uploads.product_id', '=', 'wishlists.w_product_id')
        ->where('wishlists.w_wisher_u_id', '=', $_SESSION['user'])
        ->select('wishlists.w_id AS w_id', 'uploads.image', 'products.p_id','products.p_name', 'products.p_model', 'products.p_quantity', 'products.p_price')
        ->get();

        return $this->c->view->render($response, './wishlist.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'wishlists' => $Wishlist
        ]);
    }

    public function removeFromWishlist ($request, $response, $args) {
        $w_id = $args['w_id'];

        $REMOVE_WISHLIST = Wishlist::where('w_id', $w_id)->delete([
            'w_id' => $w_id
        ]);

        return $response->withRedirect($this->c->router->pathFor('account.wishlist.page'));
    }

    public function getChangePassword($request, $response) {

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

        return $this->c->view->render($response, './change.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
        ]);
    }

    public function postChangePassword($request, $response) {
        $validation = $this->c->validator->validate($request, [
            // password_old is the name of the field of password template
            // password is also the name of the field of password template
            'password_old' => validate::noWhitespace()->notEmpty()->matchesPassword($this->c->Auth->user()->u_password),
            'password' => validate::noWhitespace()->notEmpty()
        ]);


        if($validation->failed()) {
            // if (password_verify($newPassword, $currentPassword))
            $this->c->flash->addMessage('Danger', 'Current Password does not match or you are using some not allowed characters');
            return $response->withRedirect($this->c->router->pathFor('change.password'));
        }

        // Setting the new password
         $ChangePassword = User::where('u_id', $_SESSION['user'])->update([
            'u_password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT)
        ]);

        // Flash message
        $this->c->flash->addMessage('Success', 'Your password has been changed');

        // Redirecting after password change
        return $response->withRedirect($this->c->router->pathFor('change.password'));

    }

    public function getAddresses ($request, $response) {
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

        $user = $this->c->eloquent::table('users')
        ->join('user_details', 'user_details.user_id', '=', 'users.u_id')
        ->where('users.u_id', '=', $_SESSION['user'])
        ->select('users.u_name', 'users.u_email', 'user_details.user_phone', 'user_details.user_address', 'user_details.user_city', 'user_details.user_postcode', 'user_details.user_state')
        ->first();

        return $this->c->view->render($response, './addresses.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'account' => $user
        ]);
    }

    public function getAddressEdit($request, $response, $args) {

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

        $user = $this->c->eloquent::table('users')
        ->join('user_details', 'user_details.user_id', '=', 'users.u_id')
        ->where('users.u_id', '=', $_SESSION['user'])
        ->select('users.u_name', 'users.u_email', 'user_details.user_phone', 'user_details.user_address', 'user_details.user_city', 'user_details.user_postcode', 'user_details.user_state')
        ->first();

        return $this->c->view->render($response, './address-edit.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'account' => $user
        ]);

    }

    public function postAddressEdit ($request, $response, $args) {

        $UserDetail = User_Detail::where('user_id', $_SESSION['user'])->update([
            'user_address' => $request->getParam('address'),
            'user_city' => $request->getParam('city'),
            'user_state' => $request->getParam('state'),
            'user_postcode' => $request->getParam('postcode')
        ]);

        // Flash message
        $this->c->flash->addMessage('Success', 'Address has been updated!');

        // Redirecting after password change
        return $response->withRedirect($this->c->router->pathFor('account.addresses'));
    }

    public function getResetTokenPage ($request, $response, $args) {
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

        return $this->c->view->render($response, './reset-token.twig', [
            'user' => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
            'account' => $user
        ]);
    }

}