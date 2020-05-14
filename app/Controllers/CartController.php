<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\User_Detail;
use App\Models\Product;
use App\Models\Upload;
use App\Models\Cart;
use Respect\Validation\Validator as validate;

class CartController extends Controller {
    public function addToCart($request, $response, $args) {
        $productid = $args['productid'];

        if(count(Cart::where([
            ['product_id', '=', $productid],
            ['user_id', '=', $_SESSION['user']]
        ])->get()->first()) > 0){

            $currentQuantity = Cart::where([
                ['product_id', '=', $productid],
                ['user_id', '=', $_SESSION['user']]
            ])->get()->first()->quantity;

            
            $updateCart = Cart::where([
                ['product_id', '=', $productid],
                ['user_id', '=', $_SESSION['user']]
            ])->update([
                'quantity' => ($request->getParam('quantity') + $currentQuantity)
            ]);

            $this->c->flash->addMessage('Success', 'Product is added to the cart!');

            return $response->withRedirect($this->c->router->pathFor('product.page', ['productid' => $productid]));
        
        } else {

        $cart = Cart::create([
            'user_id' => $_SESSION['user'],
            'product_id' => $productid,
            'quantity' => ($request->getParam('quantity') > 0) ? $request->getParam('quantity') : 1
        ]);

        $this->c->flash->addMessage('Success', 'Product is added to the cart!');

        return $response->withRedirect($this->c->router->pathFor('product.page', ['productid' => $productid]));
        }
    }

    public function removeFromCart($request, $response, $args) {
        $cart_id = $args['cartid'];

        $removeFromCart = Cart::where('cart_id', $cart_id)->delete([
            'cart_id' => $cart_id
        ]);

        return $response->withRedirect($this->c->router->pathFor('main.page'));
    }
}