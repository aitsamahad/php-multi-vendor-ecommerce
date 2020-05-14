<?php

namespace App\Controllers;

use App\Models\Categories;
use Respect\Validation\Validator as validate;

class CategoryController extends Controller {

    // CATEGORIES SECTION START
    public function getAddCategory($request, $response) {
        $categories = Categories::get();
        return $this->c->view->render($response, './dashboard/category.twig', [
            'categories' => $categories,
        ]);
    }

    public function postAddCategory($request, $response) {
        $categories = Categories::create(['c_name' => $request->getParam('c_name')]);

        $this->c->flash->addMessage('Success', 'New category added, check sidebar.');
        
        return $response->withRedirect($this->c->router->pathFor('category'));
    }

    public function getEditCategory($request, $response, $args) {
        $categoryid = $args['categoryid'];
        $categories = Categories::get();
        $category = Categories::where('c_id', $categoryid)->get();
        
        return $this->c->view->render($response, './dashboard/category.twig', [
            'categoryid' => $categoryid,
            'edit' => true,
            'categories' => $categories,
            'category' => $category
        ]);
    }

    public function postEditCategory($request, $response, $args) {
        $categoryid = $args['categoryid'];
        $category = Categories::where('c_id', $categoryid)->update([
            'c_name' => $request->getParam('c_name')
        ]);

        $this->c->flash->addMessage('Success', 'Updated!');
        
       return $response->withRedirect($this->c->router->pathFor('category'));
    }

    public function deleteCategory($request, $response, $args) {
        $categoryid = $args['categoryid'];
        $categories = Categories::where('c_id', $categoryid)->delete([
            'c_id' => $categoryid
        ]);
        
       return $response->withRedirect($this->c->router->pathFor('category'));
    }
    // CATEGORIES SECTION END

    public function getDashboardUsers ($request, $response, $args) {
        return $response->withRedirect($this->c->router->pathFor('dashboard.users'));
    }
}