<?php

namespace App\Controllers;

use App\Models\Question;
use App\Models\User;
use App\Models\Upload;
use App\Models\Product;
use App\Models\Product_Approval;
use App\Models\Transaction;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Store_Approval;
use App\Models\Store;
use App\Models\Store_Upload;
use Respect\Validation\Validator as validate;

class SellerController extends Controller {

    public function getSellerSection($request, $response, $args) {

        $user = User::where('u_id', $_SESSION['user'])->first();

        if($user->u_vendor == 1) return $response->withRedirect($this->c->router->pathFor('seller.dashboard'));
            return "You are not a seller, Please register as seller first to access this section";
    }
    public function getBecomeSeller($request, $response, $args) {

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

        $user = User::where('u_id', $_SESSION['user'])->first();

        return $this->c->view->render($response, './become-seller.twig', [
            $user => $user,
            $user_id => $_SESSION['user'],
            'cartDetails' => $carts,
            'cartSum' => $cartSumArray[0],
            'cartCount' => count($carts),
        ]);
        
    }

    public function postBecomeSeller($request, $response, $args) {
        
        if(count(Store::where('s_store_vendor_id', $_SESSION['user'])->get()) > 0) {
            $this->c->flash->addMessage('Danger', 'You have already applied to become a seller, please contact support for more information.');
            return $response->withRedirect($this->c->router->pathFor('become.seller')); 
        }
        
        $store_entry = Store::create([
            's_store_name' => $request->getParam('s_store_name'),
            's_store_vendor_id' => $_SESSION['user'],
            's_store_address' => $request->getParam('s_store_address')
        ]);

        $store_request = Store_Approval::create([
            'store_id' => $store_entry->id,
            'vendor_id' => $_SESSION['user']
        ]);

        $this->c->flash->addMessage('Success', 'Your request has been forwarded for moderation, Someone from our support will contact you shortly.');
        return $response->withRedirect($this->c->router->pathFor('become.seller'));
    }

    public function getProductQuestions ($request, $response, $args) {
        $productid = $args['productid'];
        
        $product_questions = $this->c->eloquent::table('questions')
        ->join('users', 'users.u_id', '=', 'questions.q_question_u_id')
        ->join('products', 'products.p_id', '=', 'questions.q_product_id')
        ->where([['questions.q_product_id', '=', $productid], ['questions.q_question_status', '=', 'Not answered']])
        ->select('questions.q_id AS q_id', 'products.p_id', 'products.p_name','questions.q_question_u_id AS user_id', 'users.u_name AS u_name', 'questions.question', 'questions.answer', 'questions.q_question_status AS q_status', 'questions.created_at')
        ->get();

        return $this->c->view->render($response, './seller/questions.twig', [
            'questions' => $product_questions
        ]);
    }

    public function getProductAnswerPage($request, $response, $args) {
        $questionid = $args['questionid'];

        $QUESTION = Question::where('q_id', $questionid)->first();

        return $this->c->view->render($response, './seller/answer.twig', [
            'question' => $QUESTION
        ]);
    }

    public function postProductAnswerPage($request, $response, $args) {
        $questionid = $args['questionid'];
        $productid = $args['productid'];

        $QUESTION_ANSWER = Question::where('q_id', $questionid)->update([
            'answer' => $request->getParam('answer'),
            'q_question_status' => 'Answered'
        ]);

        $this->c->flash->addMessage('Success', 'Question Answered!');
        return $response->withRedirect($this->c->router->pathFor('product.questions', ['productid' => $productid]));
    }

    public function getSellerProducts ($request, $response) {
        $categories = Categories::get();
        $productImages = Upload::where('store_id', $_SESSION['user'])->get();


        $products = Product::where('p_seller', $_SESSION['user'])->get();


        return $this->c->view->render($response, './seller/products.twig', [
            'categories' => $categories,
            'products' => $products,
            'productImages' => $productImages
        ]);
    }

    public function getSellerOrders($request, $response) {

        $images = Upload::where('store_id', $_SESSION['user'])->get();

        $orders = $this->c->db->prepare("SELECT products.p_id, products.p_name, orders.order_id, payment_methods.payment_name, orders.address, orders.order_note, transactions.t_id, transactions.t_quantity, transactions.t_status, transactions.created_at, transactions.t_subtotal  from orders, transactions, products, payment_methods where orders.order_id = transactions.order_id AND payment_methods.payment_id = orders.payment_method AND products.p_id = transactions.t_product_id AND transactions.t_seller = ?");
            $orders->bind_param("s", $_SESSION['user']);
            $orders->execute();
            $orders->bind_result($p_id, $p_name, $order_id, $payment_method, $address, $order_note, $t_id, $t_quantity, $t_status, $created_at, $t_subtotal);
            $ordersList = array();
            while($orders->fetch()) {
                $order = array();
                $order['p_id'] = $p_id;
                $order['p_name'] = $p_name;
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

            return $this->c->view->render($response, './seller/orders.twig', [
                'orders' => $ordersList,
                'images' => $images
            ]);
    }

    public function markAsDelivered ($request, $response, $args) {
        $TRANSACTION_ID = (int) $args['t_id'];

        $TRANSACTION = Transaction::where('t_id', $TRANSACTION_ID)->first();

        $MARK_AS_DELIVERED = Transaction::where('t_id', $TRANSACTION_ID)->update([
            't_status' => "Delivered"
        ]);

        $CURRENT_QUANTITY = Product::where('p_id', $TRANSACTION->t_product_id)->first();

        $PRODUCT_DECREMENT = Product::where('p_id', $TRANSACTION->t_product_id)->update([
            'p_quantity' => $CURRENT_QUANTITY->p_quantity - 1,
            'p_sold' => $CURRENT_QUANTITY->p_sold + 1
        ]);

        $this->c->flash->addMessage('Success', 'Order Status Updated!');
        return $response->withRedirect($this->c->router->pathFor('seller.orders'));
    }

    public function markAsCancelled ($request, $response, $args) {
        $TRANSACTION_ID = (int) $args['t_id'];


        $MARK_AS_DELIVERED = Transaction::where('t_id', $TRANSACTION_ID)->update([
            't_status' => "Cancelled"
        ]);

        $this->c->flash->addMessage('Success', 'Order Status Updated!');
        return $response->withRedirect($this->c->router->pathFor('seller.orders'));
    }

    public function getSellerProfile ($request, $response, $args) {

        $store = Store::where('s_store_vendor_id', $_SESSION['user'])->get()->first();
        $storeImage = Store_Upload::where('store_id', $store->s_id)->get()->first();

        return $this->c->view->render($response, './seller/store-profile.twig', [
            'storeid' => $store->s_id,
            'store' => $store,
            'storeImage' => $storeImage
        ]);
    }

    public function postSellerProfile($request, $response, $args) {
        $storeid = $args['storeid'];
        $store = Store::where('s_id', $storeid)->update([
            's_store_name' => $request->getParam('s_store_name'),
            's_store_address' => $request->getParam('s_store_address')
        ]);

        if ($_FILES['upload']['tmp_name']) {
            if(count(Store_Upload::where('store_id', $storeid)->get()) > 0) {
            $imageUpload = Store_Upload::where('store_id', $storeid)->update([
                'store_image' => base64_encode(file_get_contents(addslashes($_FILES['upload']['tmp_name'][0])))
            ]);
            } else  {
               $imageUpload = Store_Upload::where('store_id', $storeid)->create([
                    'store_id' => $storeid,
                    'store_image' => base64_encode(file_get_contents(addslashes($_FILES['upload']['tmp_name'][0])))
                ]); 
            }
        }


        // Sending flash message after Product successfully added
        $this->c->flash->addMessage('Success', 'Store Successfully updated!');

        // To redirect to the Produc Add page which we set in routes using setName
        return $response->withRedirect($this->c->router->pathFor('seller.profile'));
    }

    public function deleteSellerImage($request, $response, $args) {
        $storeid = $args['storeid'];

        $StoreImage = Store_Upload::where('store_id', $storeid)->update([
            'store_image' => ''
        ]);

        return $response->withRedirect($this->c->router->pathFor('seller.profile', ['storeid' => $storeid]));
    }
    

    public function getAddProduct($request, $response) {
        $categories = Categories::get();
        $brands = Brand::get();
        return $this->c->view->render($response, './seller/product-add.twig', [
            'categories' => $categories,
            'brands' => $brands
        ]);
    }

    public function postAddProduct ($request, $response) {
         // Submit the Product Add form
        // Using Validator
        $validation = $this->c->validator->validate($request, [
            'p_name' => validate::notEmpty()->length(3, 100),
            'p_description' => validate::notEmpty(),
            'p_quantity' => validate::noWhitespace()->notEmpty(),
            'p_category' => validate::notEmpty(),
            'p_brand' => validate::notEmpty(),
        ]);

        if ($validation->failed()){
            // Redirect back!
            $this->c->flash->addMessage('Danger', 'Something went wrong, please check all of the fields below!');
            return $response->withRedirect($this->c->router->pathFor('seller.add.product'));
        }

        //Using Model
        $product = Product::create([
           'p_name' => $request->getParam('p_name'),
           'p_description' => $request->getParam('p_description'),
           'p_quantity' => $request->getParam('p_quantity'),
           'p_seller' => $_SESSION['user'],
           'p_category' => $request->getParam('p_category'),
           'p_brand' => $request->getParam('p_brand'),
       ]);

       $imageUpload = Upload::create([
           'product_id' => $product->id,
           'image' => base64_encode(file_get_contents(addslashes($_FILES['upload']['tmp_name'][0])))
       ]);
       

        $store = Store::where('s_store_vendor_id', $_SESSION['user'])->first();

        // Products total increment

        $STORE_PRODUCT_COUNT = count(Product::where('p_seller', $_SESSION['user'])->get());

        $STORE_TOTAL_PRODUCT = $STORE_PRODUCT_COUNT + 1;

        $STORE_INCREMENT = Store::where('s_store_vendor_id', $_SESSION['user'])->update([
            's_store_total_products' => $STORE_TOTAL_PRODUCT 
        ]);

        $product_approval = Product_Approval::create([
            'product_id' => $product->id,
            'store_id' => $store->s_id
        ]);

        // Sending flash message after Product successfully added
        $this->c->flash->addMessage('Success', 'Please wait while your product is being moderated. Thank you for your patience. Regards, Admin.');

        // To redirect to the Produc Add page which we set in routes using setName
        return $response->withRedirect($this->c->router->pathFor('seller.add.details', ['productid' => $product->id]));
    }

    public function getAddDetails ($request, $response, $args) {
        $productid = $args['productid'];
        $PRODUCT_DETAILS = Product::where('p_id', $productid)->first();
        $CURRENT_IMAGES = Upload::where('product_id', $productid)->get();


        return $this->c->view->render($response, './seller/add-details.twig', [
            'productid' => $productid,
            'productDetails' => $PRODUCT_DETAILS,
            'productImages' => $CURRENT_IMAGES
        ]);
    }

    public function postAddDetails ($request, $response, $args) {

        $productid = (int) $args['productid'];

        $PRODUCT_DETAIL = Product::where('p_id', $productid)->update([
            'p_model' => $request->getParam('p_model'),
            'p_warranty' => $request->getParam('p_warranty'),
            'p_inside' => $request->getParam('p_inside'),
            'p_weight' => $request->getParam('p_weight'),
            'p_length' => $request->getParam('p_length'),
        ]);

         // Sending flash message after Product successfully added
         $this->c->flash->addMessage('Success', 'Details Updated! Please wait while your product is being moderated. Thank you for your patience. Regards, Admin.');

         // To redirect to the Produc Add page which we set in routes using setName
         return $response->withRedirect($this->c->router->pathFor('seller.add.details', ['productid' => $productid]));
    }

    public function uploadProductImage($request, $response, $args) {
        $productid = $args['productid'];
        $storeid = $args['storeid'];

        $PRODUCT_CURRENT_IMAGES = count(Upload::where('product_id', $productid)->get());
        if ($PRODUCT_CURRENT_IMAGES < 8) {

            $imageUpload = Upload::create([
                'product_id' => $productid,
                'image' => base64_encode(file_get_contents(addslashes($_FILES['upload']['tmp_name'][0]))),
                'store_id' => $_SESSION['user']
            ]);

         $this->c->flash->addMessage('Success', 'Image Successfully Uploaded!');
         return $response->withRedirect($this->c->router->pathFor('seller.add.details', ['productid' => $productid]));

        } else {

         $this->c->flash->addMessage('Danger', 'Maximum of 8 images can be uploaded for a product.');
         return $response->withRedirect($this->c->router->pathFor('seller.add.details', ['productid' => $productid]));
        }
    }

    public function deleteUploadedImage($request, $response, $args) {
        $productid = $args['productid'];
        $IMAGE_ID = $args['imageid'];

        $DELETE_IMAGE = Upload::where('upload_id', $IMAGE_ID)->delete([
            'upload_id' => $IMAGE_ID
        ]);

        $this->c->flash->addMessage('Success', 'Image Deleted successfully!');
        return $response->withRedirect($this->c->router->pathFor('seller.add.details', ['productid' => $productid]));
    }

    public function getEditProductPage($request, $response, $args) {
        $productid = $args['productid'];
        $PRODUCT_DETAILS = Product::where('p_id', $productid)->first();
        $CURRENT_IMAGES = Upload::where('product_id', $productid)->get();
        $categories = Categories::get();
        $brands = Brand::get();

        return $this->c->view->render($response, './seller/edit-product.twig', [
            'productid' => $productid,
            'productDetails' => $PRODUCT_DETAILS,
            'productImages' => $CURRENT_IMAGES,
            'categories' => $categories,
            'brands' => $brands
        ]);
    }

    public function postEditProductPage($request, $response, $args) {
        $productid = $args['productid'];
        $storeid = $args['storeid'];

            //Using Model
            $product = Product::where('p_id', $productid)->update([
                'p_name' => $request->getParam('p_name'),
                'p_description' => $request->getParam('p_description'),
                'p_quantity' => $request->getParam('p_quantity'),
                'p_price' => $request->getParam('p_price'),
                'p_model' => $request->getParam('p_model'),
                'p_warranty' => $request->getParam('p_warranty'),
                'p_inside' => $request->getParam('p_inside'),
                'p_weight' => $request->getParam('p_weight'),
                'p_length' => $request->getParam('p_length'),
                'p_brand' => $request->getParam('p_brand'),
                'p_category' => $request->getParam('p_category'),
            ]);

        $this->c->flash->addMessage('Success', 'Product Details updated!');
        return $response->withRedirect($this->c->router->pathFor('edit.product.page', ['productid' => $productid]));
    }

    public function uploadProductImageEdit($request, $response, $args) {
        $productid = $args['productid'];
        $storeid = (int) $args['storeid'];


        $PRODUCT_CURRENT_IMAGES = count(Upload::where('product_id', $productid)->get());
        if ($PRODUCT_CURRENT_IMAGES < 8) {

            $imageUpload = Upload::create([
                'product_id' => $productid,
                'image' => base64_encode(file_get_contents(addslashes($_FILES['upload']['tmp_name'][0]))),
                'store_id' => $storeid
            ]);

         $this->c->flash->addMessage('Success', 'Image Successfully Uploaded!');
         return $response->withRedirect($this->c->router->pathFor('edit.product.page', ['productid' => $productid]));

        } else {

         $this->c->flash->addMessage('Danger', 'Maximum of 8 images can be uploaded for a product.');
         return $response->withRedirect($this->c->router->pathFor('edit.product.page', ['productid' => $productid]));
        }
    }

    public function deleteUploadedImageEdit ($request, $response, $args) {
        $productid = $args['productid'];
        $IMAGE_ID = $args['imageid'];

        $DELETE_IMAGE = Upload::where('upload_id', $IMAGE_ID)->delete([
            'upload_id' => $IMAGE_ID
        ]);

        $this->c->flash->addMessage('Success', 'Image Deleted successfully!');
        return $response->withRedirect($this->c->router->pathFor('edit.product.page', ['productid' => $productid]));
    }

    public function deleteSellerProduct ($request, $response, $args) {

        $productid = (int) $args['productid'];

        $PRODUCT_DELETE = Product::where('p_id', $productid)->delete([
            'p_id' => $productid
        ]);

        $IMAGE_DELETE = Upload::where('product_id', $productid)->delete([
            'product_id' => $productid
        ]);

        $this->c->flash->addMessage('Success', 'Product Deleted successfully!');
        return $response->withRedirect($this->c->router->pathFor('seller.products'));
    }

    public function logout($request, $response, $args) {
        $this->c->Auth->logout();

        return $this->$response->withRedirect($this->c->router->pathFor('main.page'));
    }

    public function getDashboard ($request, $response, $args) {
        $productsApproved = Product::where([['p_seller', '=', $_SESSION['user']], ['p_status', '=', 1]])->get();
        $productsUnApproved = Product::where([['p_seller', '=', $_SESSION['user']], ['p_status', '=', 0]])->get();
        $questionsUnAnswered = Question::where([['q_seller_id', '=', $_SESSION['user']], ['q_question_status', '=', 'Not answered']])->get();
        $ordersPending = Transaction::where([['t_seller', '=', $_SESSION['user']], ['t_status', '=', 'Processing']])->get();
        return $this->c->view->render($response, './seller/dashboard.twig', [
            'productsApproved' => count($productsApproved),
            'productsUnApproved' => count($productsUnApproved),
            'questionsUnAnswered' => count($questionsUnAnswered),
            'ordersPending' => count($ordersPending),
        ]);
    }
}