<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\User_Detail;
use App\Models\Store_Upload;
use App\Models\Store;
use App\Models\Store_Approval;
use Respect\Validation\Validator as validate;

class VendorController extends Controller {

    // Vendor SECTION START
    public function getAllVendors($request, $response) {
        
        $statement = $this->c->db->prepare("SELECT stores.s_id, users.u_name, stores.s_store_name, stores.s_store_vendor_id, stores.s_store_status, stores.s_store_total_products, stores.created_at FROM users, stores WHERE users.u_id = stores.s_store_vendor_id AND users.u_vendor = 1");
        $statement->execute();
        $statement->bind_result($storeid, $vendorName, $storeName, $vendorid, $storeStatus, $totalProducts, $createdAt);
        $vendors = array();
        while($statement->fetch()){
            $vendor = array();
            $vendor['storeid'] = $storeid;
            $vendor['storeName'] = $storeName;
            $vendor['vendorName'] = $vendorName;
            $vendor['vendorid'] = $vendorid;
            $vendor['totalProducts'] = $totalProducts;
            $vendor['storeStatus'] = $storeStatus;
            $vendor['createdAt'] = $createdAt;
            array_push($vendors, $vendor);
        }
        

        return $this->c->view->render($response, './dashboard/vendors.twig', [
            'vendors' => $vendors
        ]);
    }

    public function getAddVendor($request, $response) {

        return $this->c->view->render($response, './dashboard/vendor-signup.twig');
    }

    public function postAddVendor($request, $response) {

                $imageArray = array();
        
                $length = count($imageArray);
                
                if($length>3) {
                    $this->c->flash->addMessage('Danger', 'Only 1 Pictures are allowed to upload!');
                    return $response->withRedirect($this->c->router->pathFor('add.product'));
                }
        
                //Using Model
                $user = User::create([
                    'u_name' => $request->getParam('u_name'),
                    'u_email' => $request->getParam('u_email'),
                    'u_password' => password_hash($request->getParam('u_password'), PASSWORD_DEFAULT),
                    'u_vendor' => 1
                ]);
         
                $user_detail = User_Detail::create([
                    'user_id' => $user->id,
                    'user_phone' => 'Your details',
                    'user_address' => 'Your details',
                    'user_city' => 'Your details',
                    'user_postcode' => 'Your details',
                    'user_state' => 'Your details'
                ]);

                $store = Store::create([
                    's_store_name' => $request->getParam('s_store_name'),
                    's_store_vendor_id' => $user->id,
                    's_store_address' => $request->getParam('s_store_address'),
                    's_store_status' => 1
                ]);

                $imageUpload = Store_Upload::create([
                    'store_id' => $store->id,
                    'store_image' => base64_encode(file_get_contents(addslashes($_FILES['upload']['tmp_name'][0])))
                ]);
        
        
                // Sending flash message after Product successfully added
                $this->c->flash->addMessage('Success', 'New store and vendor added!');
        
                // To redirect to the Produc Add page which we set in routes using setName
                return $response->withRedirect($this->c->router->pathFor('add.vendor'));
    }

    public function getEditVendor($request, $response, $args) {
        $storeid = $args['storeid'];
        $store = Store::where('s_id', $storeid)->get()->first();
        $storeImage = Store_Upload::where('store_id', $storeid)->get()->first();

        // var_dump($storeImage);
        // die();

        return $this->c->view->render($response, './dashboard/vendor-edit.twig', [
            'storeid' => $storeid,
            'store' => $store,
            'storeImage' => $storeImage
        ]);
    }

    public function postEditVendor($request, $response, $args) {
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
        return $response->withRedirect($this->c->router->pathFor('vendors'));
    }

    public function deleteVendor($request, $response, $args) {
        $storeid = $args['storeid'];
        $vendorid = Store::where('s_id', $storeid)->get()->first();

        $user = User::where('u_id', $vendorid->s_store_vendor_id)->update([
            'u_vendor' => 0
        ]);
        $store = Store::where('s_id', $storeid)->delete([
            's_id' => $storeid
        ]);

        $StoreImage = Store_Upload::where('store_id', $storeid)->delete([
            'store_id' => $storeid
        ]);

                // Sending flash message after Product successfully added
        $this->c->flash->addMessage('Success', 'Store Successfully Deleted!');

        // To redirect to the Produc Add page which we set in routes using setName
        return $response->withRedirect($this->c->router->pathFor('vendors'));
    }

    public function deleteVendorImage($request, $response, $args) {
        $storeid = $args['storeid'];

        $StoreImage = Store_Upload::where('store_id', $storeid)->update([
            'store_image' => ''
        ]);

        return $response->withRedirect($this->c->router->pathFor('edit.vendor', ['storeid' => $storeid]));
    }

    public function banVendor($request, $response, $args) {
        $storeid = $args['storeid'];

        $store = Store::where('s_id', $storeid)->update([
            's_store_status' => 0
        ]);

        $this->c->flash->addMessage('Success', 'Vendor is now banned from system!');

        // To redirect to the Produc Add page which we set in routes using setName
        return $response->withRedirect($this->c->router->pathFor('vendors'));
    }

    public function activateVendor($request, $response, $args) {
        $storeid = $args['storeid'];

        $store = Store::where('s_id', $storeid)->update([
            's_store_status' => 1
        ]);

        $this->c->flash->addMessage('Success', 'Vendor is now operational!');

        // To redirect to the Produc Add page which we set in routes using setName
        return $response->withRedirect($this->c->router->pathFor('vendors'));
    }

    public function getVendorRequests($request, $response) {

        // $vendor_requests = Store_Approval::get();
        $vendor_requests = $this->c->eloquent::table('store_approvals')
        ->join('stores', 'store_approvals.store_id', '=', 'stores.s_id')
        ->join('users', 'store_approvals.vendor_id', '=', 'users.u_id')
        ->select('store_approvals.store_approval_id AS id', 'stores.s_store_name AS store_name', 'stores.s_id AS store_id', 'users.u_id', 'stores.s_store_address AS store_address', 'users.u_name AS u_name')
        ->get();

        // var_dump($vendor_requests);
        // die();

        return $this->c->view->render($response, './dashboard/vendor-requests.twig', [
            'vendor_requests' => $vendor_requests
        ]);
    }

    public function approveVendorRequest($request, $response, $args) {
        $store_id = $args['storeid'];
        $vendorid = $args['vendorid'];

        $request_approved = User::where('u_id', $vendorid)->update([
            'u_vendor' => 1
        ]);

        $store_activation = Store::where('s_id', $store_id)->update([
            's_store_status' => 1
        ]);

        $request_delete = Store_Approval::where('vendor_id', $vendorid)->delete();

        
        $this->c->flash->addMessage('Success', 'Vendor is now operational!');

        // To redirect to the Produc Add page which we set in routes using setName
        return $response->withRedirect($this->c->router->pathFor('vendor.requests'));
    }

    public function rejectVendorRequest($request, $response, $args) {
        $store_id = $args['storeid'];
        $vendorid = $args['vendorid'];


        $request_delete = Store_Approval::where('vendor_id', $vendorid)->delete();

        
        $this->c->flash->addMessage('Success', 'Vendor Request rejected!');

        // To redirect to the Produc Add page which we set in routes using setName
        return $response->withRedirect($this->c->router->pathFor('vendor.requests'));
    }
    // Vendor SECTION END

}