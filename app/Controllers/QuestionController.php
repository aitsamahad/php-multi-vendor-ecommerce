<?php

namespace App\Controllers;

use App\Models\Question;

class QuestionController extends Controller {

    public function postQuestion($request, $response, $args) {
        $sellerid = $args['sellerid'];
        $productid = $args['productid'];

        $question = $request->getParam('question');

        $postQuestion = Question::create([
            'q_question_u_id' => $_SESSION['user'],
            'q_seller_id' => $sellerid,
            'q_product_id' => $productid,
            'question' => $question,
        ]);

        return $response->withRedirect($this->c->router->pathFor('product.page', ['productid' => $productid]));

    }
}