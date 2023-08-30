<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * 
 * For login related models
 * @package     Site
 * @subpackage  Base Extended
 * @category    login
 * @author      Techffodils Technologies LLP
 * @link        https://techffodils.com
 */
class Shop_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    function insertNotificationDetails($phone, $pro_id, $user_id = 0) {

        $this->db->set('user_id', $user_id)
                ->set('phone', $phone)
                ->set('pro_id', $pro_id)
                ->insert('update_notify`');

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function getAllNotification() {
        $data = array();
        $query = $this->db->select('update_notify.id,update_notify.user_id,update_notify.user_id, update_notify.phone,update_notify.pro_id,user_name,email,product_name')
                ->join('user', 'user.mlm_user_id = update_notify.user_id', 'left')
                ->join('products', 'products.id = update_notify.pro_id', 'inner')
                ->get('update_notify');
        if ($query->num_rows() > 0) {
            $i = 0;
            foreach ($query->result_array() as $row) {
                $data[$i]['id'] = $row['id'];
                $data[$i]['username'] = $row['user_name'];
                $data[$i]['email'] = $row['email'];
                $data[$i]['product_name'] = $row['product_name'];
                $data[$i]['phone'] = $row['phone'];
                $i++;
            }
        }
        return $data;
    }
    
    function deleteNotification($notification_id) {
        $this->db->where('id', $notification_id)
                ->delete('update_notify');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function getUserAddressData($user_id) {
        $data = array();
        $query = $this->db->select('user_address.id,user_address.address_1,user_address.address_2,user_address.city,user_address.country_id,user_address.state_id,user_address.zip_code,address_type,first_name,last_name,phone_number')
                ->join('user_details', 'user_details.mlm_user_id = user_address.mlm_user_id', 'left')
                ->where('user_address.mlm_user_id',$user_id)
                ->order_by('user_address.status','desc')
                ->get('user_address');
        if ($query->num_rows() > 0) {
            $i=0;
            foreach ($query->result_array() as $row) {
                $data[$i]['address_id'] = $row['id'];
                $data[$i]['address_1'] = $row['address_1'];
                $data[$i]['address_2'] = $row['address_2'];
                $data[$i]['city'] = $row['city'];
                $data[$i]['country'] = $row['country_id'];
                $data[$i]['country_name'] = $this->helper_model->getCountryName($row['country_id']);
                $data[$i]['zip_code'] = $row['zip_code'];
                $data[$i]['address_type'] = $row['address_type'];
                $data[$i]['fullname'] = $row['first_name'].''.$row['last_name'];
                $data[$i]['phone_number'] = $row['phone_number'];
                $data[$i]['state'] = $row['state_id'];
                $data[$i]['state_name'] = $this->helper_model->getStateName($row['state_id']);
                $i++;
            }
        }
        return $data;
    }
    
    
      function insertOrder($user_id, $post, $cart, $total_items, $total_amount, $total_pv, $order_status,$upload_data) {

        $order_id = $discount = 0;
        if($post['shop_checkout'] == 'shop_checkout_home'){
            $type = 'home_delivery';
        }else{
            $type = 'click_collect';
            // $discount = $total_amount*0.01;
            // $total_amount -= $discount;
            
        }
        $address = $this->getUserAddress($post['chooseAddress']);
// Array ( [chooseAddress] => 2 [payment_type] => cash_on_delivery [shop_checkout] => shop_checkout_click )
//Array ( [chooseAddress] => 2 [payment_type] => bank_transfer [shop_checkout] => shop_checkout_click )
        

        $username = $this->helper_model->getUserFullName($user_id);
        $phone = $this->helper_model->IdToPhone($user_id);
        $res = $this->db->set('user_id ', $user_id)
                ->set('total_amount', $total_amount)
                ->set('total_pv ', $total_pv)
                ->set('product_count', $total_items)
                ->set('payment_type', $post['payment_type'])
                ->set('order_date', date("Y-m-d H:i:s"))
                ->set('confirm_date', date("Y-m-d H:i:s"))
                ->set('name', $username)
                ->set('phone', $phone)
                ->set('address1', isset($address['address_1'])?$address['address_1']:null)
                ->set('address2', isset($address['address_2'])?$address['address_2']:null)
                ->set('country', isset($address['country_id'])?$address['country_id']:null)
                ->set('state', isset($address['state_id'])?$address['state_id']:null)
                ->set('city', isset($address['city'])?$address['city']:null)
                ->set('zip_code', isset($address['zip_code'])?$address['zip_code']:null)
                ->set('order_status', 5)
                ->set('delivery_charge', 0)
                ->set('shipping_charge', 0)
                ->set('tax', 0)
                ->set('type', $type)
                ->set('discount', $discount)
                ->set('images', serialize($upload_data))
                ->insert('orders');
        if ($res) {
            $order_id = $this->db->insert_id();
            foreach ($cart as $c) {
                $expiry_date = date("Y-m-d H:i:s");
               
                $this->db->set('order_id ', $order_id)
                        ->set('product_id', $c['id'])
                        ->set('quantity ', $c['qty'])
                        ->set('amount', $c['price'])
                        ->set('pv', $c['pv'])
                        ->set('image', $c['image'])
                        ->set('date ', date("Y-m-d H:i:s"))
                        ->set('options', serialize($c['options']))
                       ->set('option_value', serialize($c['option_value']))
                        ->insert('order_products');

                $this->updateQuantity($c['id'],$c['qty']);
            }
           
            // if (isset($post['address_1'])) {
            //     $this->db->set('address_1', $post['address_1'])
            //             ->set('city', $post['city'])
            //             ->set('zip_code', $post['zip_code'])
            //             ->set('country_id', $post['country'])
            //             ->set('state_id', $post['state'])
            //             ->set('address_type', $post['inlineRadioOptions'])
            //             ->where('mlm_user_id', $user_id)
            //             ->update('user_address');
            // }

        }
        return $order_id;
    }

function getAllProductNames($query) {
        $data = array();
        if ($query != '') {
            $res = $this->db->select("id,product_name,product_amount,images")
                    ->from('products')
                    ->where('status',1)
                    ->like('product_name', trim($query))
                    ->get();
        } else {
            $res = $this->db->select("product_name")
                    ->from('products')
                    ->where('status',1)
                    ->get();
        }
       $json=[];
        foreach ($res->result_array() as $row) {
            $prod_img =unserialize($row['images']);
            $image = (!empty($prod_img)&& is_array($prod_img))?$prod_img[0]['file_name']:'no-image.jpg';
            $data['image']= $image;
            $data['name'] = $row['product_name'];
            $data['product_amount'] = $row['product_amount'];
            $data['url'] = base_url().'product-details/'.$row['id'];
            
            $json[]=$data;
        }
        return json_encode($json);        
    }



 function checkSeoKeyExists($seo_key){
    $data=[];
    $res = $this->db->select("*")
                    ->from('seo_url')
                    ->where('seo_keyword',$seo_key)
                    ->get();
    if($res->num_rows() > 0){
        $data['status']=true;
        $data['seo_key']=$res->row()->seo_key;
        $data['seo_value']=$res->row()->seo_value;
        $data['seo_url']=base_url().$res->row()->seo_key.'/'.$res->row()->seo_value;
    }

    return $data;
 }



    function addUserAddress($data, $user_id) {
        // $this->db->set('mlm_user_id', $user_id)
        //     ->set('address_1', $data['address_1'])
        //     ->set('address_2', $data['address_2'])
        //     ->set('city', $data['city'])
        //     ->set('country_id', $data['country_id'])
        //     ->set('state_id', $data['state'])
        //     ->set('zip_code', $data['zip_code'])
        //     ->set('address_type', $data['address_type'])
        //     ->set('status', $data['status'])
        //     ->insert('user_address');
            if($data['status']==1){
                $data1 = [
                        'address_1' => $data['address_1'],
                        'address_2' => $data['address_2'],
                        'city' => $data['city'],
                        'country_id' => $data['country_id'],
                        'state_id' => $data['state'],
                        'zip_code' =>$data['zip_code'],
                    ];
                $this->db->where('mlm_user_id', $user_id);
                $this->db->update('user_details', $data1);

                $data2=[
                    'status' => 0,

                ];
                 $this->db->where('mlm_user_id', $user_id);
                 $this->db->where('status', 1);
                $this->db->update('user_address', $data2);


                $this->db->set('mlm_user_id', $user_id)
                ->set('address_1', $data['address_1'])
                ->set('address_2', $data['address_2'])
                ->set('city', $data['city'])
                ->set('country_id', $data['country_id'])
                ->set('state_id', $data['state'])
                ->set('zip_code', $data['zip_code'])
                ->set('address_type', $data['address_type'])
                ->set('status', $data['status'])
                ->insert('user_address');
            }else{

                $this->db->set('mlm_user_id', $user_id)
                ->set('address_1', $data['address_1'])
                ->set('address_2', $data['address_2'])
                ->set('city', $data['city'])
                ->set('country_id', $data['country_id'])
                ->set('state_id', $data['state'])
                ->set('zip_code', $data['zip_code'])
                ->set('address_type', $data['address_type'])
                ->set('status', $data['status'])
                ->insert('user_address');
            }
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
    
    function getAllAddress($user_id) {
        $data = array();
        $query = $this->db->select("*")
                ->from("user_address")
                ->where('mlm_user_id', $user_id)
                ->get();
        $i = 0;
        foreach ($query->result_array() as $row) {
            $data[$i]['address_1'] = lang($row['address_1']);
            $data[$i]['address_2'] = lang($row['address_2']);
            $data[$i]['city'] = lang($row['city']);
            $data[$i]['country_name'] = $this->shop_model->getCountryName($row['country_id']);
            $data[$i]['state_name'] = $this->shop_model->getStateName($row['state_id']);
            $data[$i]['zip_code'] = lang($row['zip_code']);
            $data[$i]['address_type'] = lang($row['address_type']);
            $data[$i]['id'] = $row['id'];
            $i++;
        }
        return $data;
    }
     function getCountryName($country_id){
       
        $query = $this->db->select('country_name')
                ->from("countries")
                ->where('id', $country_id)
                ->get();
                        $i = 0;
       if ($query->num_rows() > 0) {
            $country_name = $query->row()->country_name;
        }
        return $country_name;

     }

     function getStateName($state_id){
       
        $query = $this->db->select('state_name')
                ->from("states")
                ->where('id', $state_id)
                ->get();
                        $i = 0;
       if ($query->num_rows() > 0) {
            $state_name = $query->row()->state_name;
        }
        return $state_name;

     }


    function UpdateUserAddress($data, $user_id) {
        // $this->db->set('mlm_user_id', $user_id)
        //     ->set('address_1', $data['address_1'])
        //     ->set('address_2', $data['address_2'])
        //     ->set('city', $data['city'])
        //     ->set('country_id', $data['country_id'])
        //     ->set('state_id', $data['state'])
        //     ->set('zip_code', $data['zip_code'])
        //     ->set('address_type', $data['address_type'])
        //     ->set('status', $data['status'])
        //     ->where('id', $data['update_address'])
        //     ->update('user_address');
            if($data['status']==1){
                $data1 = [
                        'address_1' => $data['address_1'],
                        'address_2' => $data['address_2'],
                        'city' => $data['city'],
                        'country_id' => $data['country_id'],
                        'state_id' => $data['state'],
                        'zip_code' =>$data['zip_code'],
                    ];
                $this->db->where('mlm_user_id', $user_id);
                $this->db->update('user_details', $data1);

                 $data2=[
                    'status' => 0,

                ];
                 $this->db->where('mlm_user_id', $user_id);
                 $this->db->where('status', 1);
                $this->db->update('user_address', $data2);

                $this->db->set('mlm_user_id', $user_id)
                ->set('address_1', $data['address_1'])
                ->set('address_2', $data['address_2'])
                ->set('city', $data['city'])
                ->set('country_id', $data['country_id'])
                ->set('state_id', $data['state'])
                ->set('zip_code', $data['zip_code'])
                ->set('address_type', $data['address_type'])
                ->set('status', $data['status'])
                ->where('id', $data['update_address'])
                ->update('user_address');

            }else{
            $this->db->set('mlm_user_id', $user_id)
                ->set('address_1', $data['address_1'])
                ->set('address_2', $data['address_2'])
                ->set('city', $data['city'])
                ->set('country_id', $data['country_id'])
                ->set('state_id', $data['state'])
                ->set('zip_code', $data['zip_code'])
                ->set('address_type', $data['address_type'])
                ->set('status', $data['status'])
                ->where('id', $data['update_address'])
                ->update('user_address');
        }
        if ($this->db->affected_rows() > 0) {

            return true;
        }

        return false;
    }

    function getUserAddress($add_id) {
        $data = array();
        $query = $this->db->select("*")
                ->from("user_address")
                ->where('id', $add_id)
                ->limit(1)
                ->get();
        foreach ($query->result_array() as $row) {
            $data['address_1'] = lang($row['address_1']);
            $data['address_2'] = lang($row['address_2']);
            $data['city'] = lang($row['city']);
            $data['country_id'] = lang($row['country_id']);
            $data['state_id'] = lang($row['state_id']);
            $data['zip_code'] = lang($row['zip_code']);
            $data['address_type'] = lang($row['address_type']);
            $data['status'] = lang($row['status']);
            $data['id'] = $row['id'];
        }
        return $data;
    }


    // Your Orders

    function getUserOrdersData($user_id){ 
    $this->load->model('member_model');       
        $data = array();
        $query = $this->db->select('orders.id, orders.order_status,orders.total_amount,orders.order_date,orders.product_count,user_name')
        ->join('user', 'user.mlm_user_id = orders.user_id', 'inner')
        ->where('orders.user_id', $user_id)
        ->get('orders');
        if ($query->num_rows() > 0) {
            $i = 0;
            foreach ($query->result_array() as $row) {
                // $data[$i]['order_id'] = 'MB00'.$row['id'];
                $data[$i]['order_id'] = $row['id'];
                $data[$i]['customer'] = $row['user_name'];
                $data[$i]['order_status'] = lang($this->member_model->getOrderStatus($row['order_status']));
                $data[$i]['order_date'] = $row['order_date'];
                $data[$i]['total_amount'] = $this->helper_model->currency_conversion(round($row['total_amount'], 8));
                $data[$i]['product_count'] = $row['product_count'];
                $i++;
            }
        }
        return $data;
    }

    function optionValueName($option_value_id) { 
      
        $data = array();
        if($option_value_id){
            $query = $this->db->select('option_values.option_id,option_values.option_value,options.option_name')
                    ->join('option_values', 'option_values.id = product_option_values.option_value')
                    ->join('options', 'options.id = product_option_values.option_id')
                    ->where_in('product_option_values.id', $option_value_id)
                    ->get('product_option_values');
            if ($query->num_rows() > 0) {

                foreach ($query->result_array() as $row) {
                    $data[$row['option_name']] = $row['option_value'];
                }
            }
         }
        return $data;
    }

     function deleteUserAddress($add_id) {
       
            $this->db->where('id', $add_id)
                    ->delete('user_address');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return true;
        }


        // function getAllAddress($user_id){
        //     $data=array();
        //     $query=$this->db->select('user_address.id,user_address.address_1,user_address.address_2,user_address.city,user_address.zip_code,countries.country_name')
        //     ->join('countries','countries.id = user_address.country_id','left')
        //      ->where('user_address.mlm_user_id', $user_id)
        //      ->get('user_address');

        //       if ($query->num_rows() > 0) {
        //     $i = 0;
        //     foreach ($query->result_array() as $row) {
        //         // $data[$i]['order_id'] = 'MB00'.$row['id'];
        //         $data[$i]['address_1'] = $row['address_1'];
        //         $data[$i]['address_2'] = $row['address_2'];
        //         $data[$i]['city'] = $row['city'];
        //         $data[$i]['zip_code'] = $row['zip_code'];
        //         $data[$i]['country_name'] = $row['country_name'];
        //         $i++;
        //     }
        // }
        // return $data;

        // }

    // function decreaseProQuantity($pro_id,$qty) {
    //     $quantity = 0;
    //     $query = $this->db->select('quantity')
    //             ->where('id', $pro_id)
    //             ->limit(1)
    //             ->get('products');
    //     if ($query->num_rows() > 0) {
    //         $quantity = $query->row()->quantity;
    //         $updated_qty = $quantity-$qty;
    //         $qty_update = $this->updateQuantity($pro_id,$updated_qty);
    //     }

    // }

     public function updateQuantity($pro_id,$qty) {
         $this->db->set('quantity ', "quantity -$qty",false)
                ->where('id ', $pro_id)
                ->update('products');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

}

?>
