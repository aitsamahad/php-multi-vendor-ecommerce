<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Upload;
use Respect\Validation\Validator as validate;

class DashboardController extends Controller {

    // PRODUCT SECTION START
    public function getAddProduct($request, $response) {
        $categories = Categories::get();
      
        return $this->c->view->render($response, './dashboard/product-add.twig', [
            'categories' => $categories,
        ]);
    }

    public function postAddProduct($request, $response) {
        // Submit the Product Add form
        // Using Validator
        $validation = $this->c->validator->validate($request, [
            'p_name' => validate::notEmpty()->length(3, 100),
            'p_description' => validate::notEmpty(),
            'p_quantity' => validate::noWhitespace()->notEmpty(),
            'p_category' => validate::notEmpty(),
        ]);

        if ($validation->failed()){
            // Redirect back!
            $this->c->flash->addMessage('Danger', 'Something went wrong, please check all of the fields below!');
            return $response->withRedirect($this->c->router->pathFor('add.product'));
        }

        $imageArray = array();
       
        if(isset($_POST['submit'])){
            if(count($_FILES['upload']['name']) > 0){
                //Loop through each file
                for($i=0; $i<count($_FILES['upload']['name']); $i++) {
                  //Get the temp file path
                    $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
                    
                    //Make sure we have a filepath
                    if($tmpFilePath != ""){
                        $image = base64_encode(file_get_contents(addslashes($tmpFilePath)));
                        //save the filename
                        $shortname = $_FILES['upload']['name'][$i];
                        array_push($imageArray, $image);

                      }
                }
            }
        }

        $length = count($imageArray);
        
        if($length>3) {
            $this->c->flash->addMessage('Danger', 'Only 3 Pictures are allowed to upload!');
            return $response->withRedirect($this->c->router->pathFor('add.product'));
        }

        //Using Model
        $product = Product::create([
           'p_name' => $request->getParam('p_name'),
           'p_description' => $request->getParam('p_description'),
           'p_quantity' => $request->getParam('p_quantity'),
           'p_seller' => 1,
           'p_category' => $request->getParam('p_category'),
       ]);

       for ($i=0; $i<$length; $i++){
       $imageUpload = Upload::create([
           'product_id' => $product->id,
           'image' => $imageArray[$i]
       ]);
       }


        // Sending flash message after Product successfully added
        $this->c->flash->addMessage('Success', 'Please wait while your product is being moderated. Thank you for your patience. Regards, Admin.');

        // To redirect to the Produc Add page which we set in routes using setName
        return $response->withRedirect($this->c->router->pathFor('add.product'));
    }

    public function getEditProduct($request, $response, $args) {
        $productid = $args['productid'];
        $categories = Categories::get();
        $product = Product::where('p_id', $productid)->get();
        $images = Upload::where('product_id', $productid)->get();


        // $statement = $this->c->db->prepare("SELECT `upload_id`, `image` FROM `uploads` WHERE product_id = 1");
        // $statement->execute();
        // $statement->bind_result($upload_id, $DBimage);
        // $SQLimages = array();
        // while($statement->fetch()){
        //     $image = array();
        //     $image['upload_id'] = $upload_id;
        //     $image['image'] = $DBimage;
        //     array_push($SQLimages, $image);
        // }

        return $this->c->view->render($response, './dashboard/product-edit.twig', [
            'productid' => $productid,
            'categories' => $categories,
            'product' => $product,
            'images' => $images,
        ]);
    }

    public function postEditProduct($request, $response, $args) {
        $productid = $args['productid'];
        $product = Product::where('p_id', $productid)->update([
            'p_name' => $request->getParam('p_name'),
            'p_description' => $request->getParam('p_description'),
            'p_quantity' => $request->getParam('p_quantity'),
            'p_category' => $request->getParam('p_category')
        ]);

        $this->c->flash->addMessage('Success', 'Your product is now updated!');
        
        return $response->withRedirect($this->c->router->pathFor('edit.product', ['productid' => $productid]));
        // return $response->withRedirect($this->c->router->pathFor('edit.product', ['productid' => $productid], ['productid' => $productid]));
    }
    // PRODUCT SECTION END

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
    

    // BRAND SECTION START
    public function getAddBrand($request, $response) {
        $brands = Brand::get();
        return $this->c->view->render($response, './dashboard/brand.twig', [
            'brands' => $brands,
        ]);
    }

    public function postAddBrand($request, $response) {
        $brand = Brand::create([
            'b_name' => $request->getParam('b_name'),
            'b_description' => $request->getParam('b_description')
            ]);

        $this->c->flash->addMessage('Success', 'New brand has been added, check sidebar.');
        
        return $response->withRedirect($this->c->router->pathFor('brand'));
    }

    public function getEditBrand($request, $response, $args) {
        $brandid = $args['brandid'];
        $brands = Brand::get();
        $brand = Brand::where('b_id', $brandid)->get();
        
        return $this->c->view->render($response, './dashboard/brand.twig', [
            'brandid' => $brandid,
            'edit' => true,
            'brands' => $brands,
            'brand' => $brand
        ]);
    }

    public function postEditBrand($request, $response, $args) {
        $brandid = $args['brandid'];
        $brand = Brand::where('b_id', $brandid)->update([
            'b_name' => $request->getParam('b_name'),
            'b_description' => $request->getParam('b_description')
        ]);
        
        $this->c->flash->addMessage('Success', 'Updated!');
        return $response->withRedirect($this->c->router->pathFor('brand'));
    }

    public function deleteBrand($request, $response, $args) {
        $brandid = $args['brandid'];
        $brands = Brand::where('b_id', $brandid)->delete([
            'b_id' => $brandid
        ]);
        
       return $response->withRedirect($this->c->router->pathFor('brand'));
    }
    // BRAND SECTION END

}