<?php

namespace App\Controllers;

use App\Models\Brand;
use Respect\Validation\Validator as validate;

class BrandController extends Controller {

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