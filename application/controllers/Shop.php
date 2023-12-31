<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once 'admin/Base_Controller.php';

/**
 * @author Techffodils Technologies LLP
 * Class Login
 */
class Shop extends Base_Controller {

    /**
     * loading index page.
     * @author Techffodils Technologies LLP
     */

     public function __construct(){
        parent::__construct();
        header('Last-Modified:'.gmdate('D, d M Y H:i:s').'GMT');
        header('Cache-Control: no-cache,must-revalidate,max-age=0');
        header('Cache-Control: post-check=0,pre-check=0',false);
        header('Pragma: no-cache');
    }
    public function index($product_seo_key = '') {

        $prod_view_flag = 0;
        $products = [];

        if ($product_seo_key != '') {
            $check_seo_key = $this->shop_model->checkSeoKeyExists($product_seo_key);
            if (is_array($check_seo_key) && isset($check_seo_key['seo_value'])) {
                $pro_id = $check_seo_key['seo_value'];
                $prod_view_flag = 1;

                $this->load->model('product_model');
                if ($pro_id) {
                    $products = $this->product_model->getProductDetailsView($pro_id);
                }
            }
        }

        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);

        $this->load->model('site_management_model');
        $slider_images = $this->site_management_model->getSliderLists();
        
        
        // if ($this->aauth->getId()) {
        //     $this->loadPage('', 'dashboard');
        // }
        // if ($this->input->post('login') && $this->site_login()) {
        //     $this->helper_model->insertActivity(($this->aauth->getUserType() == 'employee') ? $this->base_model->getAdminUserId() : $this->aauth->getId(), 'user_login');
        //     $this->loadPage('', 'dashboard');
        // }
        // if ($this->dbvars->CAPTCHA_STATUS > 0 || $this->aauth->get_login_attempts() > $this->dbvars->CAPTCHA_LOGIN)
        //     $this->setData('CAPTCHA_STATUS', 1);
        // $this->setData('key', $key);
        // $this->setData('login_error', $this->form_validation->error_array());
        // $this->setData('EMPLOYEE_STATUS', $this->dbvars->EMPLOYEE_STATUS);
        // $this->setData('AFFILIATES_STATUS', $this->dbvars->AFFILIATES_STATUS);
        // $this->setData('sytem_title', $this->helper_model->getSystemPath());
        // $this->setData('title', lang('login'));

        $this->load->model('product_model');
        $nav_category = $this->product_model->getNavCategoryLists();
        $cart = $this->cart->contents();
        $cart_amount = $this->cart->total();

        $deals_of_the_day = $this->product_model->getAllDealOftheDayProducts();
        $popular_categories = $this->product_model->getAllPopularCategories();
        // print_r($popular_categories);die;
        $this->setData('deals_of_the_day', $deals_of_the_day);
        $this->setData('popular_categories', $popular_categories);
        $this->setData('cart', $cart);
        $this->setData('items', $this->cart->total_items());
        $this->setData('cart_amount', $cart_amount);
        $this->setData('nav_category', $nav_category);
        $this->setData('slider_images', $slider_images);
        $this->setData('prod_view_flag', $prod_view_flag);
        $this->setData('products', $products);
        $this->loadView();
    }

    // public function get_products($name){
    // }


    public function login_register() {
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);
        $this->load->model('product_model');
        $nav_category = $this->product_model->getNavCategoryLists();

        $this->setData('nav_category', $nav_category);
        $this->loadView();
    }

    public function shop($cat_id = "") {
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);
        $user_type = $this->aauth->getUserType();

        $this->load->model('product_model');

        $min_amt=$max_amt='';
        $brand=[];
        $color=[];
        $brands = $this->product_model->getAllBrands();
        $colors = $this->product_model->getAllColors();

        if ($this->input->post('filter_btn') || null !== $this->input->post('min_amt')) {
            
            $this->load->helper('security');
            $post = $this->security->xss_clean($this->input->post());
 
            $brand=(isset($post['brand']))?$post['brand']:[];
            $color=(isset($post['color']))?$post['color']:[];
            $min_amt = $post['min_amt'];
            $max_amt = $post['max_amt'];
            $this->session->set_userdata('session_brand' , $brand);
            $this->session->set_userdata('session_color' , $color);

        }
        $set_brands=(null !==$this->session->userdata('session_brand'))?$this->session->userdata('session_brand'):[]; 
        $set_colors=(null !==$this->session->userdata('session_color'))?$this->session->userdata('session_color'):[]; 

        $brand=$set_brands;
        $color=$set_colors;
        $nav_category = $this->product_model->getNavCategoryLists();
        $pro = $this->product_model->getProductsCount($cat_id,$min_amt,$max_amt,$brand,$color);
        $this->load->library('pagination');
        $config = array();
        $config['base_url'] = base_url() . "shop/" . $cat_id;
        $config['total_rows'] = $pro;
        $config['per_page'] = 8;
        $config['num_links'] = 2;
        $config["uri_segment"] = 3;
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';
        $config['attributes'] = ['class' => 'page-link'];
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only"></span></span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $pagination["links"] = $this->pagination->create_links();
        $products = '';
        if ($cat_id) {
            $products = $this->product_model->getProducts($cat_id,$min_amt,$max_amt,$brand,$color,$config['per_page'], $page);

        }
      
        $cat_name = $this->product_model->getCatName($cat_id);
        $this->setData('min_amt', $min_amt);
        $this->setData('max_amt', $max_amt);
        $this->setData('brand', $brand);
        $this->setData('color', $color);
        $this->setData('brands', $brands);
        $this->setData('colors', $colors);
        $this->setData('cat_name', $cat_name);
        $this->setData('session_brands', $set_brands);
        $this->setData('session_colors', $set_colors);
        $this->setData('nav_category', $nav_category);
        $this->setData('products', $products);
        $this->setData('link', $pagination["links"]);
        $this->setData('items', $this->cart->total_items());

        $this->loadView();
    }


    public function cart() {
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);
        $this->load->model('product_model');
        $this->load->model('cart_model');
        $nav_category = $this->product_model->getNavCategoryLists();
        $products = $this->cart_model->getAllProducts();

        $user_name = $this->aauth->getUserName();
        $user_type = $this->aauth->getUserType();
        $user_id = ($this->aauth->getUserType() == 'employee') ? $this->base_model->getAdminUserId() : $this->aauth->getId();

        $cart = $this->cart->contents();
        // print_r($cart);die;
        $items = $this->cart->total_items();
        if ($items == 0) {
            $this->loadPage(lang('Cart Is Empty'), './', 'warning');
        }
        $cart_amount = $this->cart->total();
        $pro_count = count($cart);

        $this->setData('user_id', $user_id);
        $this->setData('pro_count', $pro_count);
        $this->setData('cart', $cart);
        $this->setData('cart_amount', $cart_amount);
        $this->setData('nav_category', $nav_category);
        $this->setData('products', $products);
        $this->setData('items', $items);
        $this->setData('items_count', $items);
        $this->setData('total_items_amount', $this->cart->total());
        $this->loadView();
    }

    public function about_us() {
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);
        $this->load->model('product_model');
        $nav_category = $this->product_model->getNavCategoryLists();

        $this->setData('nav_category', $nav_category);
        $this->loadView();
    }

    public function warranty() {
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);
        $this->load->model('product_model');
        $nav_category = $this->product_model->getNavCategoryLists();

        $this->setData('nav_category', $nav_category);
        $this->loadView();
    }

    public function contact() {
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);
        $this->load->model('product_model');
        $nav_category = $this->product_model->getNavCategoryLists();

        $this->setData('nav_category', $nav_category);
        $this->loadView();
    }

    public function app() {
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);
        $this->load->model('product_model');
        $nav_category = $this->product_model->getNavCategoryLists();

        $this->setData('nav_category', $nav_category);
        $this->loadView();
    }

    public function shop_details($pro_id = "") {
        
        $this->session->unset_userdata('product_option_data');
        $party_id = 0;
        $this->load->model('product_model');
        $nav_category = $this->product_model->getNavCategoryLists();
        $products = '';
        if ($pro_id) {
            $products = $this->product_model->getProductDtls($pro_id);
            $party_cart = $this->cart->contents();
            // foreach ($party_cart as $key => $c) {
            //     if (!in_array($c['id'], $products)) {
            //         $this->cart->remove($key);
            //     }
            // }
        } else {
            // $this->loadPage(lang('invalid_party'), 'shop-details', 'danger');
        }
        $option_data = $this->product_model->getProductOptionData($pro_id);
        // print_r($option_data);die;
        $this->setData('option_data', $option_data);
        $this->setData('party_id', $party_id);
        $this->setData('nav_category', $nav_category);
        $this->setData('products', $products);
        $this->setData('items', $this->cart->total_items());
        $this->setData('party_cart', $party_cart);
        $this->setData('total_items_amount', $this->cart->total());
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);
        $this->setData('TITLE', '  '.$products[0]['product_name']);

        $this->loadView();
    }

    public function product_details($pro_id = '') {

        $party_id = 0;
        $this->load->model('product_model');
        $nav_category = $this->product_model->getNavCategoryLists();
        $products = '';
        if ($pro_id) {
            $products = $this->product_model->getProductDetailsView($pro_id);
            // print_r($products);die;
            $party_cart = $this->cart->contents();
            // foreach ($party_cart as $key => $c) {
            //     if (!in_array($c['id'], $products)) {
            //         $this->cart->remove($key);
            //     }
            // }
        } else {
            // $this->loadPage(lang('invalid_party'), 'shop-details', 'danger');
        }
        $this->setData('party_id', $party_id);
        $this->setData('nav_category', $nav_category);
        $this->setData('products', $products);
        $this->setData('items', $this->cart->total_items());
        $this->setData('party_cart', $party_cart);
        $this->setData('total_items_amount', $this->cart->total());
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);
        $this->loadView();
    }

    public function checkout() {
        
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $user_id = $this->aauth->getId();

        $this->setData('user_name', $user_name);
        $this->load->model('product_model');

        $nav_category = $this->product_model->getNavCategoryLists();
        $cart = $this->cart->contents();
        $total_items = $this->cart->total_items();
        $total_amount = $this->cart->total();
        $total_pv = $this->cart->total_pv();

        if(empty($cart)){
            $this->loadPage('', 'shop','danger');
        }
       
        if ($this->input->post('shop_checkout')) {
            $this->load->helper('security');
            $checkout_data = $this->security->xss_clean($this->input->post());

                $config['upload_path'] = FCPATH . 'assets/images/';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $new_name = 'slip_' . time();
                $config['file_name'] = $new_name;
                $this->load->library('upload', $config);
                $upload_data = '';

                if ($this->upload->do_upload('document')) {
                    $upload_data = $this->upload->data();
                    $slip = $upload_data['file_name'];
                    $extension = $upload_data['file_ext'];
                    $file_size = $upload_data['file_size'];

                    if ($this->dbvars->IMAGE_RESIZE_STATUS) {
                        if (isset($upload_data['full_path'])) {
                            $this->load->library('image_lib');
                            $configer = array(
                                'image_library' => 'gd2',
                                'source_image' => $upload_data['full_path'],
                                // 'maintain_ratio' => TRUE,
                                // // 'width' => 500,
                                // // 'height' => 500,
                            );
                            $this->image_lib->initialize($configer);
                            if (!$this->image_lib->resize()) {
                                $error['reason'] = $this->image_lib->display_errors();
                                $this->helper_model->insertFailedActivity($user_id, 'resize_fail', $error);
                            }
                            $this->image_lib->clear();
                        }
                    }
                }

            $fullname = $this->helper_model->getUserFullName($user_id);
            $email = $this->helper_model->getUserEmailId($user_id);
            $phone_number = $this->helper_model->getCompleteMobileNumber($user_id);

            $payment_status = FALSE;
            $order_status = 0;
            $payment_method = $checkout_data['payment_type'];

            if ($payment_method == 'Thawani') {
                $order_status = 0;
                $payment_status = TRUE;
            } else if ($payment_method == "cash_on_delivery") {
                $order_status = 1;
                $payment_status = TRUE; 
            } elseif ($payment_method == "bank_transfer") {
                $order_status = 0;
                $payment_status = TRUE;
            }

            if ($payment_status) {
                // if($checkout_data['shop_checkout'] == 'shop_checkout_home'){
                  $order_id = $this->shop_model->insertOrder($this->aauth->getId(), $checkout_data, $cart, $total_items, $total_amount, $total_pv, $order_status,$upload_data);  
                // } else {
                //     $this->loadPage('Update address from account', 'checkout', 'danger');
                // }

                if ($order_id) {
                    $encrypt_id = $this->helper_model->encode($order_id);
                    
                    if ($order_status >= 0) {
                        $this->cart->destroy();
                        $this->loadPage('', 'order_success/' . $encrypt_id);
                    } else{

                        if($payment_method =='bank_transfer'){
                            $this->setData('user_name', $user_name);
                            $this->loadPage('', 'order_success/' . $encrypt_id);
                        }

                        // $thawani = new \s4d\payment\thawani([
                        //     'isTestMode' => 1, ## set it to 0 to use the class in production mode  
                        //     'public_key' => 'HGvTMLDssJghr9tlN9gr4DVYt0qyBy',
                        //     'private_key' => 'rRQ26GcsZzoEhbrP2HZvLYDbn9C9et',
                        // ]);

                        // $url = $thawani->generatePaymentUrl([
                        //     'client_reference_id' => rand(1000, 9999) . $order_id,
                        //     'products' => [
                        //         ['name' => $user_name, 'unit_amount' => $total_amount, 'quantity' => $total_items],
                        //     ],
                        //     'success_url' => $thawani->currentPageUrl() . '?op=checkPayment',
                        //     'cancel_url' => $thawani->currentPageUrl() . '?op=checkPayment',
                        //     'metadata' => [
                        //         'order_id' => $order_id,
                        //         'customer_name' => $fullname,
                        //         'customer_phone' => $email,
                        //         'customer_email' => $phone_number
                        //     ]
                        // ]);

                        // if (!empty($url) && $thawani->payment_id) {
                        //     $_SESSION['session_id'] = $thawani->payment_id;
                        //     header('Location:' . $url);
                        // }
                    }
                } else {
                    $this->loadPage('Something went wrong', 'checkout', 'danger');
                }
            }
        }

        $user_address_data = $this->shop_model->getUserAddressData($this->aauth->getId());
        $country = '';
        if (isset($user_address_data['country'])) {
            $country = $user_address_data['country'];
        }

        // print_r($user_address_data);die;

        $states = $this->helper_model->getAllStates($country);
        $countries = $this->helper_model->getAllCountries();
        $this->setData('countries', $countries);
        $this->setData('states', $states);
        $this->setData('user_name', $user_name);
        $this->setData('user_address', $user_address_data);
        $this->setData('nav_category', $nav_category);
        $this->setData('cart', $cart);
        $this->setData('cart_amount',$total_amount);
        
        $this->loadView();
    }

    function checkPaymentStatus() {
        $check = $thawani->checkPaymentStatus($_SESSION['session_id']);
        if ($thawani->payment_status == 1) {
            //update order status
            $this->loadPage(lang('checkout_success'), 'checkout');
            ## successful payment  
        } else {
            ## failed payment  
            $this->loadPage(lang('checkout_failed'), 'checkout', 'danger');
        }
    }
    public function account($active = '', $action = '', $add_id = '') {
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $active = $active;
        $this->load->model('report_model');
        $user = $this->aauth->get_user();
        $user_id = $this->aauth->getId();

        if($user_id==''){
            $this->loadPage('Logout Successfully', 'login-site' );
        }
        if ($this->input->post('change_password') && $this->validate_general_password()) {
            $active = 5;
            $this->load->helper('security');
            $post = $this->security->xss_clean($this->input->post());
            $hash_pass = $this->aauth->hash_password($post['current_password'], '');
            if ($this->aauth->verify_password($user->password, $hash_pass)) {
                $res = $this->report_model->updatepassword($post, $user_id);
                if ($res) {
                    $this->loadPage('Update success', 'account/' . $active, 'success');
                }
            } else {
                $this->loadPage('Current password does not match', 'account/' . $active, 'danger');
            }
        }

        if ($this->input->post('account_details') && $this->validate_general_update()) {
            $active = 4;
            $this->load->helper('security');
            $post = $this->security->xss_clean($this->input->post());
            $res = $this->report_model->updategeneral($post, $user_id);
            if ($res) {
                $this->loadPage('Update success', 'account/' . $active, 'success');
            } else {

                $this->loadPage('Something went wrong', 'account/' . $active, 'danger');
            }
        }

        $edit_flag = FALSE;
        if ($this->input->post('add_address')) {
            $active = 3;
            $this->load->helper('security');
            $post = $this->security->xss_clean($this->input->post());
            $res = $this->shop_model->addUserAddress($post, $user_id);
            $country_id = $post['country_id'];
            $countries = $this->helper_model->getAllCountries();
            $states = $this->helper_model->getAllStates($country_id);




            $this->setData('states', $states);
            $this->setData('countries', $countries);
            $this->loadPage('Address Added Successfully', 'account/' . $active, 'success');
        }


        if ($action && $add_id) {
            // $active = 3;
            if ($action == "edit") {
                 
                $edit_flag = TRUE;
                $usr_addr = $this->shop_model->getUserAddress($add_id);
                $country_id = $usr_addr['country_id'];
                $countries = $this->helper_model->getAllCountries();
                $states = $this->helper_model->getAllStates($country_id);

                $this->setData('states', $states);
                $this->setData('countries', $countries);
                $this->setData('usr_addr', $usr_addr);
                $this->setData('edit_flag', $edit_flag);
            } elseif ($action == "delete") {
                
                $edit_flag = TRUE;

                $res = $this->shop_model->deleteUserAddress($add_id);
               
                $this->loadPage('Update Address Successfully', 'account/' . $active, 'success');
              
            } else {
                $this->loadPage('invalid_action', 'account/' . $active, 'danger');
            }
        }

        if ($this->input->post('update_address')) {
            $active = 3;
            $this->load->helper('security');
            $post = $this->security->xss_clean($this->input->post());
            $res = $this->shop_model->UpdateUserAddress($post, $user_id);
            $country_id = $usr_addr['country_id'];
            $countries = $this->helper_model->getAllCountries();
            $states = $this->helper_model->getAllStates($country_id);

            $this->setData('countries', $countries);
            $this->setData('usr_addr', $usr_addr);
            $this->loadPage('Update Address Successfully', 'account/' . $active, 'success');
        }

        $user_detail = $this->db->select('first_name,last_name,phone_number')
                ->from('user_details')
                ->where('mlm_user_id', $user_id)
                ->get();

        foreach ($user_detail->result_array() as $row) {
            $detail = $row;
        }

// print_r($detail);die;
        $this->load->model('product_model');
        $nav_category = $this->product_model->getNavCategoryLists();

        $address = $this->shop_model->getAllAddress($user_id);
        $usr_addr = $this->shop_model->getUserAddress($add_id);
        $countries = $this->helper_model->getAllCountries();
        $user_orders = $this->shop_model->getUserOrdersData($user_id);
        $this->setData('user_orders', $user_orders);
        $this->setData('countries', $countries);
        $this->setData('usr_addr', $usr_addr);
        $this->setData('edit_flag', $edit_flag);
        $this->setData('add_id', $add_id);
        $this->setData('address', $address);
        $this->setData('login_error', $this->form_validation->error_array());
        $this->setData('active', $active);
        $this->setData('user_name', $user_name);
        $this->setData('user_mail', $user->email);
        $this->setData('detail', $detail);
        $this->setData('nav_category', $nav_category);
        $this->setData('login_error', $this->form_validation->error_array());
        $this->loadView();
    }

    public function validate_general_password() {
        $this->form_validation->set_rules('current_password', lang('password'), 'trim|required');
        $this->form_validation->set_rules('password', lang('password'), 'trim|required|matches[confirm_password]|min_length[6]');
        $this->form_validation->set_rules('confirm_password', lang('confirm_password'), 'trim|required|min_length[6]');
        $this->form_validation->set_error_delimiters('<li>', '</li>');
        $validation = $this->form_validation->run();
        return $validation;
    }

    public function validate_general_update() {
        $this->form_validation->set_rules('email', lang('email'), 'required');
        $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
        $this->form_validation->set_rules('phone_number', lang('phone_number'), 'trim|required|numeric');
        $this->form_validation->set_error_delimiters('<li>', '</li>');
        $validation = $this->form_validation->run();
        return $validation;
    }

    function update_notify() {
        $logged_user = $this->aauth->getId($this->aauth->getUserType() == 'employee') ? $this->base_model->getAdminUserId() : $this->aauth->getId();
        if ($logged_user == '') {
            $logged_user = $this->base_model->getAdminUserId();
        }
        $this->load->helper('security');
        $post = $this->security->xss_clean($this->input->get());
        if ($post['phone']) {
            $phone = $post['phone'];
            $pro_id = $post['pro_id'];
            $res = $this->shop_model->insertNotificationDetails($phone, $pro_id, $logged_user);
            if ($res) {
                $log_user = ($this->aauth->getUserType() == 'employee') ? $this->base_model->getAdminUserId() : $this->aauth->getId();
                $this->helper_model->insertActivity($log_user, 'update_notify', $post);
                echo 'yes';
                exit;
            }
        }
        echo 'no';
        exit;
    }

    public function products() {
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $this->setData('user_name', $user_name);

        $this->load->model('product_model');

        $min_amt=$max_amt='';
        $brand=[];
        $color=[];
        $category=[];

        if ($this->input->post('filter_btn') || null !== $this->input->post('min_amt')) {
            
            $this->load->helper('security');
            $post = $this->security->xss_clean($this->input->post());
 
            $brand=(isset($post['brand']))?$post['brand']:[];
            $color=(isset($post['color']))?$post['color']:[];
            $category=(isset($post['category']))?$post['category']:[];
            $min_amt = $post['min_amt'];
            $max_amt = $post['max_amt'];
            $this->session->set_userdata('session_brand' , $brand);
            $this->session->set_userdata('session_color' , $color);
            $this->session->set_userdata('session_category' , $category);

        }
        $set_brands=(null !==$this->session->userdata('session_brand'))?$this->session->userdata('session_brand'):[]; 
        $set_color=(null !==$this->session->userdata('session_color'))?$this->session->userdata('session_color'):[];
        $set_category=(null !==$this->session->userdata('session_category'))?$this->session->userdata('session_category'):[]; 

        $brand=$set_brands;
        $color=$set_color;
        $category=$set_category;


        $nav_category = $this->product_model->getNavCategoryLists();
        $products = $this->product_model->getAllProCount($min_amt,$max_amt,$brand,$category,$color);
        $cart = $this->cart->contents();
        $cart_amount = $this->cart->total();


        $this->load->library('pagination');
        $config = array();
        $config['base_url'] = base_url() . "products";
        $config['total_rows'] = $products;
        $config['per_page'] = 8;
        $config['num_links'] = 2;
        $config["uri_segment"] = 2;


        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';
        $config['attributes'] = ['class' => 'page-link'];
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only"></span></span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $pagination["links"] = $this->pagination->create_links();

        $products = $this->product_model->getAllProducts($config['per_page'], $page, $min_amt,$max_amt,$brand,$category,$color);
        $brands = $this->product_model->getAllBrands();
        $categories = $this->product_model->getAllCaegories();
        $colors = $this->product_model->getAllColors();
        $this->setData('link', $pagination['links']);
        $this->setData('cart', $cart);
        $this->setData('items', $this->cart->total_items());
        $this->setData('cart_amount', $cart_amount);
        $this->setData('nav_category', $nav_category);
        $this->setData('products', $products);
        $this->setData('brands', $brands);
        $this->setData('min_amt', $min_amt);
        $this->setData('max_amt', $max_amt);
        $this->setData('session_brands', $set_brands);
        $this->setData('session_category', $set_category);
        $this->setData('session_colors', $set_color);
        $this->setData('categories', $categories);
        $this->setData('colors', $colors);

        $this->loadView();
    }

    function get_products() {

        $query = $this->input->get('query');
        $result = $this->shop_model->getAllProductNames($query);
        echo $result;
        exit();
    }

    function user_invoice($ord_id) {
        $active = 2;
        $this->load->model('member_model');
        $invoice_details = $this->member_model->getInvoiceDetails($ord_id);
        $pro_total = $invoice_details['products']['0']['product_total'];
        // $matches = array();
        // preg_match('/\d+/', $pro_total, $matches);
        // if (isset($matches[0])) {
        //       $number = intval($matches[0]); 
        // } else {
        //       $number = 0;
        // }
        // $vat = ($number * 5)/100;
        // $total = $invoice_details['total_amount'];
        // preg_match('/\d+/', $total, $matches);
        // if (isset($matches[0])) {
        //       $total_amount = intval($matches[0]); 
        // } else {
        //       $total_amount = 0;
        // }
        // $grand_total = $total_amount;
                $total = $invoice_details['total_amount'];

        if (!$invoice_details) {
            $this->loadPage(lang('invalid_link'), 'account/' . $active, 'warning');
        }
        $this->setData('invoice_details', $invoice_details);
        $this->setData('active', $active);
        // $this->setData('vat', $vat);
        $this->setData('grand_total', $total);
        $this->loadView();
    }


    public function order_success($order_id) {
        $this->load->model('product_model');
        $decrypt_id = $this->helper_model->decode($order_id);
        $user_name = ($this->aauth->getUserType() == 'employee') ? $this->helper_model->getAdminUsername() : $this->aauth->getUserName();
        $nav_category = $this->product_model->getNavCategoryLists();

        $this->setData('nav_category', $nav_category);
        $this->setData('user_name', $user_name);
        $this->setData('order_id', $decrypt_id);      
        $this->loadView();
    }

}
