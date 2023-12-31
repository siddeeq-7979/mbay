<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * For Based on product related function
 * @package     Site
 * @subpackage  Base Extended
 * @category    Registration
 * @author      Techffodils Technologies LLP
 * @link        https://techffodils.com
 */
class Product_model extends CI_Model {

    /**
     * For add new product and modification also do here like activate,inactivate,delete 
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $data
     * @param type $images
     * @return type
     */
    // function addProduct($data, $images = array()) {
    //     // print_r($images = array());die;
    //     $currency_ratio = 1;
    //     if ($this->dbvars->MULTI_CURRENCY_STATUS > 0) {
    //         $currency_ratio = $this->main->get_usersession('mlm_data_currency', 'currency_ratio');
    //     }
    //     $inv_cat = $investment_amount = $expiry_date = 0;
    //     if (isset($data['inv_cat'])) {
    //         $inv_cat = $data['inv_cat'];
    //     }
    //     if (isset($data['investment_amount'])) {
    //         $investment_amount = $data['investment_amount'];
    //     }
    //     if (isset($data['expiry_date'])) {
    //         $expiry_date = $data['expiry_date'];
    //     }
    //     $product_code = $this->genrateProductCode();
    //     $special = isset($data['special']) ? 1 : 0;
    //     $this->db->set('product_name', $data['product_name'])
    //             ->set('quantity', $data['quantity']);
    //     // if ($this->dbvars->BASIC_CART_STATUS) {
    //         $this->db->set('category', $data['category']);
    //                 // ->set('sub_category', $data['sub_category']);
    //     // }
    //     $this->db->set('special', $special)
    //             ->set('description', $data['description'])
    //             ->set('product_amount', $data['product_amount'] / $currency_ratio)
    //             // ->set('product_pv', $data['product_pv'])
    //             ->set('product_code', $product_code)
    //             // ->set('recurring_type', $data['recurring_type'])
    //             // ->set('product_type', $data['product_type'])
    //             ->set('images', serialize($images))
    //             // ->set('inv_cat', $inv_cat)
    //             // ->set('investment_amount', $investment_amount)
    //             ->set('sort_order', $data['sort_order'])
    //             ->set('keyword', $data['keyword'])
    //             ->set('expiry_date', $expiry_date)
    //             ->set('date', date("Y-m-d H:i:s"))
    //             ->insert('products');
    //     if ($this->db->affected_rows() > 0) {
    //         return true;
    //     }
    //     return false;
    // }

    function addProduct($data, $images = array()) {
        $deal_of_the_day = isset($data['deal_of_the_day']) ? 1 : 0;
        $this->db->set('product_name', $data['product_name'])
                ->set('description', $data['description'])
                ->set('product_amount', $data['product_amount'])
                ->set('quantity', $data['quantity'])
                ->set('category', $data['category'])
                ->set('brand', $data['brand'])
                ->set('sort_order', $data['sort_order'])
                ->set('images', serialize($images))
                // ->set('keyword', $data['keyword'])
                ->set('deal_of_the_day', $deal_of_the_day)
                ->set('date', date("Y-m-d H:i:s"))
                ->insert('products');

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * For generate the product code automatically
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @return type
     */
    function genrateProductCode($count = 6) {
        //$this->load->helper('string');
        $code = '';
        $flag = 1;
        while ($flag) {
            //$code = 'Pro-'.random_string('alpha', $count);
            $code = 'Pro-' . rand(100000, 1000000);
            if (!$this->productExist($code)) {
                $flag = 0;
            }
        }
        return $code;
    }

    /**
     * For check the product code exits or not
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $code
     * @return type
     */
    public function productExist($code) {
        return $this->db->select("id")
                        ->from("products")
                        ->where('product_code', $code)
                        ->count_all_results();
    }

    /**
     * For getting all products
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @return type
     */

    function getAllProducts($limit='',$offset='',$min_amt='',$max_amt='',$brand=[],$category=[],$color=[]) {

        $data = array();
         $this->db->select("pro.id,status,product_name,product_amount,product_pv,product_code,recurring_type,product_type,description,images,brand,category");

                $this->db->from("products as pro");
                $this->db->join("product_option_values as pro_opt", 'pro_opt.pro_id = pro.id', 'left');
                $this->db->join("option_values as opt", 'opt.id = pro_opt.option_value', 'left');
                $this->db->group_by("pro.id");


                if($limit!=''&& $offset!='')
                {
                  $this->db->limit($limit,$offset);
                }

                if($min_amt!='' && $max_amt!='' && is_array($brand) && is_array($category) && is_array($color) && count($brand)>0 && count($category)>0 && count($color)>0){

                    $this->db->where('product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("brand",$brand);
                    $this->db->where_in("category",$category);
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt==''&& $max_amt==''&& is_array($brand) && is_array($category) && is_array($color) && (count($brand)>0) && (count($category)>0) && (count($color)>0)){
                    $this->db->where_in("brand",$brand);
                    $this->db->where_in("category",$category);
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt=='' && $max_amt=='' && is_array($brand) && is_array($category) && (count($brand)>0) && (count($category)>0)){
                    $this->db->where_in("brand",$brand);
                    $this->db->where_in("category",$category);
                }elseif($min_amt=='' && $max_amt=='' && is_array($brand) && is_array($color) && (count($brand)>0) && (count($color)>0)){
                    $this->db->where_in("brand",$brand);
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt=='' && $max_amt=='' && is_array($color) && is_array($category) && (count($color)>0) && (count($category)>0)){
                    $this->db->where_in("opt.id",$color);
                    $this->db->where_in("category",$category);
                }elseif($min_amt==''&& $max_amt==''&& is_array($brand) && (count($brand)>0)){
                    $this->db->where_in("brand",$brand);
                }elseif($min_amt==''&& $max_amt=='' && is_array($category)&& (count($category)>0)){
                    $this->db->where_in("category",$category);
                }elseif($min_amt==''&& $max_amt=='' && is_array($color)&& (count($color)>0)){
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt!='' && $max_amt!='' && is_array($brand) && is_array($category) && (count($brand)>0) && (count($category)>0)){
                    $this->db->where('product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("brand",$brand);
                    $this->db->where_in("category",$category);
                }elseif($min_amt!='' && $max_amt!='' && is_array($brand) && is_array($color) && (count($brand)>0) && (count($color)>0)){
                    $this->db->where('product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("brand",$brand);
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt!='' && $max_amt!='' && is_array($color) && is_array($category) && (count($color)>0) && (count($category)>0)){
                    $this->db->where('product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("opt.id",$color);
                    $this->db->where_in("category",$category);
                }elseif($min_amt!=''&& $max_amt!='' && is_array($brand)&& (count($brand)>0)){
                    $this->db->where('product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("brand",$brand);
                }elseif($min_amt!=''&& $max_amt!='' && is_array($category)&& (count($category)>0)){
                    $this->db->where('product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("category",$category);
                }elseif($min_amt!=''&& $max_amt!='' && is_array($color)&& (count($color)>0)){
                    $this->db->where('product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt!=''&& $max_amt!=''){
                    $this->db->where('product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                }elseif($min_amt!=''){
                    $this->db->where('product_amount >=',$min_amt);
                }elseif($max_amt!=''){
                    $this->db->where('product_amount <=',$max_amt);
                }
                
                $res =$this->db->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $option_data = $this->defaultOptionData($row->id);
            $data[$i]['sl_no'] = $i + 1;
            $data[$i]['id'] = $row->id;
            $data[$i]['product_name'] = $row->product_name;
            $data[$i]['product_amount'] = $row->product_amount+ $option_data['option_amount'];
            $data[$i]['product_pv'] = $row->product_pv;
            $data[$i]['product_code'] = $row->product_code;
            $data[$i]['recurring_type'] = $row->recurring_type;
            $data[$i]['product_type'] = $row->product_type;
            $data[$i]['status'] = $row->status;
            $data[$i]['description'] = $row->description;
            $data[$i]['files'] = $this->getAllFiles($row->images);
            $data[$i]['brand'] = $row->brand;
            $data[$i]['brand_name'] = $this->getBrandName($row->brand);
            $data[$i]['options'] = implode(",",$option_data['option_value']);
            $data[$i]['category'] = $row->category;
            $i++;

        }
        return $data;
    }

    /**
     * For get product details
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $prod_id
     * @return type
     */
    function getProductDetails($prod_id) {
        $data = array();
        $res = $this->db->select("id,status,product_name,brand,description,images,product_amount,product_pv,product_code,recurring_type,product_type,inv_cat,investment_amount,expiry_date,quantity,special,category,sub_category, sort_order, keyword,deal_of_the_day")
                ->from("products")
                ->where('id', $prod_id)
                ->limit(1)
                ->get();
        foreach ($res->result() as $row) {
            $data['id'] = $row->id;
            $data['product_name'] = $row->product_name;
            $data['product_amount'] = $row->product_amount;
            $data['product_pv'] = $row->product_pv;
            $data['product_code'] = $row->product_code;
            $data['recurring_type'] = $row->recurring_type;
            $data['product_type'] = $row->product_type;
            $data['status'] = $row->status;
            // $data['files'] = $this->getAllFiles($row->images);
            $data['files'] = $row->images;
            $data['inv_cat'] = $row->inv_cat;
            $data['investment_amount'] = $row->investment_amount;
            $data['expiry_date'] = $row->expiry_date;
            $data['description'] = $row->description;
            $data['quantity'] = $row->quantity;
            $data['special'] = $row->special;
            $data['category'] = $row->category;
            $data['brand'] = $row->brand;
            $data['sub_category'] = $row->sub_category;
            $data['sort_order'] = $row->sort_order;
            $data['keyword'] = $row->keyword;
            $data['deal_of_the_day'] = $row->deal_of_the_day;
        }
        return $data;
    }



    /**
     * For all existing files
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $prod_id
     * @return type
     */
    function getAllExistingfiles($prod_id) {
        $files = array();
        $res = $this->db->select("images")
                ->from("products")
                ->where('id', $prod_id)
                ->get();
        foreach ($res->result() as $row) {
            $files = $this->getAllFiles($row->images);
        }
        return $files;
    }

    /**
     * for  getting all files
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $images
     * @return type
     */
    function getAllFiles($images) {
        $image = unserialize($images);
        $files = array();
        $i = 0;
        if(is_array($image) || is_object($image)) {
            foreach($image as $key => $img) {
                $files[$i]['id'] = $key;
                $files[$i]['file_name'] = $img['file_name'];
                $i++;
            }
        }
        return $files;
    }


    /**
     * For update the product details
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $data
     * @param type $files
     * @return type
     */
    function updateProduct($data, $files = array()) {
        // print_r($files));die;
        $currency_ratio = 1;
        if ($this->dbvars->MULTI_CURRENCY_STATUS > 0) {
            $currency_ratio = $this->main->get_usersession('mlm_data_currency', 'currency_ratio');
        }
        $inv_cat = $investment_amount = $expiry_date = 0;
        if (isset($data['inv_cat'])) {
            $inv_cat = $data['inv_cat'];
        }
        if (isset($data['investment_amount'])) {
            $investment_amount = $data['investment_amount'];
        }
        if (isset($data['expiry_date'])) {
            $expiry_date = $data['expiry_date'];
        }
        $special = isset($data['special']) ? 1 : 0;
        $this->db->set('product_name', $data['product_name'])
                ->set('quantity', $data['quantity']);
        // if ($this->dbvars->BASIC_CART_STATUS) {
        //     $this->db->set('category', $data['pro_category'])
        //             ->set('sub_category', $data['sub_category']);
        // }
        $deal_of_the_day = isset($data['deal_of_the_day']) ? 1 : 0;
        $this->db->set('special', $special)
                ->set('description', $data['description'])
                ->set('brand', $data['brand'])
                ->set('product_amount', $data['product_amount'] / $currency_ratio)
                // ->set('product_pv', $data['product_pv'])
                // ->set('recurring_type', $data['recurring_type'])
                // ->set('product_type', $data['product_type'])
                ->set('inv_cat', $inv_cat)
                ->set('investment_amount', $investment_amount)
                ->set('expiry_date', $expiry_date)
                ->set('keyword', $data['keyword'])
                ->set('category', $data['category'])
                ->set('sort_order', $data['sort_order'])
                ->set('deal_of_the_day', $deal_of_the_day)
                // ->set('keyword', $data['keyword'])
                ->set('sort_order', $data['sort_order'])
                ->set('category', $data['category'])
                ->set('images', serialize($files))
                ->where('id', $data['update_product'])
                ->update('products');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return true;
    }

    /**
     * For change product status
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $product_id
     * @param type $status
     * @return type
     */
    function changeProductStatus($product_id, $status) {
        $this->db->set('status', $status)
                ->where('id', $product_id)
                ->update('products');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * For delete the product 
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $product_id
     * @return type
     */
    function deleteProduct($product_id) {
        $this->db->where('id', $product_id)
                ->delete('products');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return true;
    }

    /**
     * for getting the investment categories
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @return type
     */
    function getInvestmentCategories() {
        $data = array();
        $res = $this->db->select("id,name")
                ->from("investment_category")
                ->where('status', 1)
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['name'] = $row->name;
            $i++;
        }
        return $data;
    }

    /**
     *  For getting count of product list
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @return type
     */
    function countOfProductList() {
        return $this->db->select('id')
                        ->from("products")
                        ->count_all_results();
    }

    /**
     * For getting the product list column name
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @return type
     */
    function getTableColumnProductList() {
        return array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'product_code', 'dt' => 1),
            array('db' => 'product_name', 'dt' => 2),
            array('db' => 'status', 'dt' => 3),
            array('db' => 'product_amount', 'dt' => 4),
            array('db' => 'product_pv', 'dt' => 5),
            array('db' => 'product_type', 'dt' => 6),
            array('db' => 'images', 'dt' => 7),
        );
    }

    /**
     * For getting all product list
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $table
     * @param type $where
     * @return type
     */
    function getTotalFilteredProductList($table, $where) {
        $query = $this->db->query("select * from " . $table . " " . $where . "");
        return $query->num_rows();
    }

    /**
     * For getting all data product list
     * 
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @param type $table
     * @param type $column
     * @param type $where
     * @param type $order
     * @param type $limit
     * @return type
     */
    function getResultDataProductList($table, $column, $where, $order, $limit) {
        $data = array();
        $query = $this->db->query("select " . implode(',', $column) . " from " . $table . " " . $where . " " . $order . " " . $limit);
        foreach ($query->result_array() as $key => $val) {
            $data[] = array_values($val);
        }
        return $data;
    }

    /**
     *  For getting count of product Ordered
     * @author Techffodils Technologies LLP
     * @copyright (c) 2017, Techffodils Technologies

     * @return count
     */
    function checkProductOrdered($pro_id) {
        return $this->db->select('id')
                        ->from("order_products")
                        ->where('product_id', $pro_id)
                        ->count_all_results();
    }

    function getAllCaegories() {
        $data = array();
        $res = $this->db->select("id,category")
                ->from("category")
                ->where('status', '1')
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['category'] = $row->category;
            $i++;
        }
        return $data;
    }

    function getAllBrands() {
        $data = array();
        $res = $this->db->select("id,brand_name")
                ->from("brand_settings")
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['brand_name'] = $row->brand_name;
            $i++;
        }
        return $data;
    }

    function getAllSubCategories($cat_id) {
        $data = array();
        $res = $this->db->select("id,sub_category")
                ->from("subcategory")
                ->where('status', '1')
                ->where('category_id', $cat_id)
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['category'] = $row->sub_category;
            $i++;
        }
        return $data;
    }

    function getProductCategory($prod_id) {
        $data = lang('na');
        $query = $this->db->select("ca.category,su.sub_category")
                ->from("products as pro")
                ->join("category as ca", 'ca.id = pro.category', 'inner')
                ->join("subcategory as su", 'su.id = pro.sub_category', 'left')
                ->where('pro.id', $prod_id)
                ->limit(1)
                ->get();
        if ($query->num_rows() > 0) {
            $data = $query->row()->category;
            if ($query->row()->sub_category) {
                $data .= ' - ' . $query->row()->sub_category;
            }
        }
        return $data;
    }

    function getProductDescription($prod_id) {
        $files = array();
        $res = $this->db->select("description")
                ->from("products")
                ->where('id', $prod_id)
                ->get();
        foreach ($res->result() as $row) {
            $files = $row->description;
        }
        return $files;
    }

    function getProductImage($prod_id) {
        $files = array();
        $res = $this->db->select("images")
                ->from("products")
                ->where('id', $prod_id)
                ->get();
        foreach ($res->result() as $row) {
            $files = $row->images;
        }
        return $files;
    }
    function addCategory($data, $cat_image) {
        $cat_nav = isset($data['slider']) ? 1 : 0;
        $popular_category = isset($data['popular_category']) ? 1 : 0;
        $this->db->set('category', $data['category'])
                ->set('description', $data['description'])
                ->set('parent', $data['parent'])
                ->set('sort_order', $data['sort_order'])
                ->set('image', serialize($cat_image))
                ->set('cat_nav',$cat_nav)
                ->set('keyword', $data['keyword'])
                ->set('popular_category', $popular_category)
                ->set('creation_date', date("Y-m-d H:i:s"))
                ->insert('category');

                $this->db->last_query();

        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function getCatDetails($id) {
        $data = array();
        $res = $this->db->select("category,description,parent,sort_order,image,keyword,cat_nav,popular_category")
                ->from("category")
                ->where('id', $id)
                ->limit(1)
                ->get();
        foreach ($res->result() as $row) {
            $data['category'] = $row->category;
            $data['description'] = strip_tags($row->description);
            $data['parent'] = $row->parent;
            $data['sort_order'] = $row->sort_order;
            $data['cat_nav'] = $row->cat_nav;
            $data['image'] = $row->image;
            $data['keyword'] = $row->keyword;
            $data['popular_category'] = $row->popular_category;
        }
        return $data;
    }
    
    function updateCategory($data, $cat_image) {
        $cat_nav = isset($data['slider']) ? 1 : 0;
        $featured = isset($data['featured']) ? 1 : 0;
        $popular_category = isset($data['popular_category']) ? 1 : 0;
        $this->db->set('category', $data['category'])
                ->set('description', $data['description'])
                ->set('parent', isset($data['parent'])?$data['parent']:'')
                ->set('sort_order', $data['sort_order'])
                ->set('cat_nav',$cat_nav)
                ->set('image', serialize($cat_image))
                ->set('keyword', $data['keyword'])
                ->set('popular_category', $popular_category)
                ->set('creation_date', date("Y-m-d H:i:s"))
                ->where('id', $data['update_cat'])
                ->update('category');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function deleteCategory($id) {
        $this->db->where('id', $id)
                ->delete('category');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function changeCatStatus($id, $status) {
        $this->db->set('status', $status)
                ->where('id', $id)
                ->update('category');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function addSubCategory($data, $cat_image) {
        $this->db->set('category_id', $data['category'])
                ->set('sub_category', $data['sub_cat_name'])
                ->set('order', $data['sub_cat_order'])
                ->set('creation_date', date("Y-m-d H:i:s"))
                ->set('image', $cat_image)
                ->insert('subcategory');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function getSubCatDetails($id) {
        $data = array();
        $res = $this->db->select("category_id,sub_category,order,image")
                ->from("subcategory")
                ->where('id', $id)
                ->limit(1)
                ->get();
        foreach ($res->result() as $row) {
            $data['category_id'] = $row->category_id;
            $data['sub_cat_name'] = $row->sub_category;
            $data['sub_cat_order'] = $row->order;
            $data['image'] = $row->image;
        }
        return $data;
    }

    function updateSubCategory($data, $cat_image) {
        $this->db->set('category_id', $data['category'])
                ->set('sub_category', $data['sub_cat_name'])
                ->set('order', $data['sub_cat_order'])
                ->set('updation_date', date("Y-m-d H:i:s"))
                ->set('image', $cat_image)
                ->where('id', $data['update_sub_cat'])
                ->update('subcategory');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function deleteSubCategory($id) {
        $this->db->where('id', $id)
                ->delete('subcategory');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function changeSubCatStatus($id, $status) {
        $this->db->set('status', $status)
                ->where('id', $id)
                ->update('subcategory');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function catIdToName($id) {
        $category = NULL;
        $query = $this->db->select('category')
                ->where('id', $id)
                ->limit(1)
                ->get('category');
        if ($query->num_rows() > 0) {
            $category = $query->row()->category;
        }
        return $category;
    }

    public function subCatIdToName($id) {
        $category = NULL;
        $query = $this->db->select('sub_category')
                ->where('id', $id)
                ->limit(1)
                ->get('subcategory');
        if ($query->num_rows() > 0) {
            $category = $query->row()->sub_category;
        }
        return $category;
    }

    function getTableColumnCategoryList() {
        return array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'category', 'dt' => 1),
            array('db' => 'description', 'dt' => 2),
            array('db' => 'sort_order', 'dt' => 3),
            array('db' => 'creation_date', 'dt' => 4),
            array('db' => 'status', 'dt' => 5),
        );
    }

    function countOfCategoryList() {
        return $this->db->select('id')
                        ->from("category")
                        ->count_all_results();
    }

    public function getCatOrder($id) {
        $order = 0;
        $query = $this->db->select('order')
                ->where('id', $id)
                ->limit(1)
                ->get('category');
        if ($query->num_rows() > 0) {
            $order = $query->row()->order;
        }
        return $order;
    }

    function getTableColumnSubCategoryList() {
        return array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'sub_category', 'dt' => 1),
            array('db' => 'category_id', 'dt' => 2),
            array('db' => 'creation_date', 'dt' => 3),
            array('db' => 'updation_date', 'dt' => 4),
            array('db' => 'status', 'dt' => 5),
        );
    }

    function countOfSubCategoryList() {
        return $this->db->select('id')
                        ->from("subcategory")
                        ->count_all_results();
    }

    public function getSubCatOrder($id) {
        $order = 0;
        $query = $this->db->select('order')
                ->where('id', $id)
                ->limit(1)
                ->get('subcategory');
        if ($query->num_rows() > 0) {
            $order = $query->row()->order;
        }
        return $order;
    }

    public function getCategoryImage($id) {
        $image = 'cat-banner-1.jpg';
        $query = $this->db->select('image')
                ->where('id', $id)
                ->limit(1)
                ->get('category');
        if ($query->num_rows() > 0) {
            $image = $query->row()->image;
        }
        return $image;
    }

    public function getSubCategoryImage($id) {
        $image = 'cat-banner-2.jpg';
        $query = $this->db->select('image')
                ->where('id', $id)
                ->limit(1)
                ->get('subcategory');
        if ($query->num_rows() > 0) {
            $image = $query->row()->image;
        }
        return $image;
    }

    function getCategoryLists() {
        $data = array();
        $res = $this->db->select("id, category, description, sort_order,keyword, creation_date")
                ->from("category")
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['category'] = $row->category;
            $data[$i]['description'] = strip_tags($row->description);
            $data[$i]['sort_order'] = $row->sort_order;
            $data[$i]['keyword'] = $row->keyword;
            $data[$i]['creation_date'] = $row->creation_date;
            $i++;
        }
        return $data;
    }

    function getProductLists() {
        $data = array();
        $res = $this->db->select("id, product_name, description, product_amount, category, quantity")
                ->from("products")
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['product_name'] = $row->product_name;
            $data[$i]['category'] = $row->category;
            $data[$i]['description'] = $row->description;
            $data[$i]['product_amount'] = $row->product_amount;
            $data[$i]['quantity'] = $row->quantity;
            $i++;
        }
        return $data;
    }

    function updateimage($product_id,$newimage){
        $this->db->set('image', serialize($newimage))
                 ->where('id', $product_id)
                 ->update('category');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;

    }

    function updateimageproduct($product_id,$newimage){
        $this->db->set('images', serialize($newimage))
                 ->where('id', $product_id)
                 ->update('products');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;

    }

    function getNavCategoryLists() {
        $data = array();
        $res = $this->db->select("id, category, description, sort_order, creation_date")
                ->from("category")
                ->where('cat_nav', 1)
                ->order_by("sort_order", "desc")
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['category'] = $row->category;
            $data[$i]['description'] = $row->description;
            $data[$i]['sort_order'] = $row->sort_order;
            $data[$i]['creation_date'] = $row->creation_date;
            $i++;
        }
        return $data;
    }

    function getProducts($cat_id,$min_amt='',$max_amt='',$brand=[],$color=[]) {

        $data = array();
        $this->db->select("pro.id, pro.product_name, pro.category, pro.description, pro.product_amount, pro.product_pv, pro.quantity, pro.sort_order, pro.keyword, pro.images, pro.brand");
          
        $this->db->from("products as pro");
        $this->db->join("category as cat", 'cat.id = pro.category', 'left');
        $this->db->join("product_option_values as pro_opt", 'pro.id = pro_opt.pro_id', 'left');
        $this->db->join("option_values as opt","opt.id=pro_opt.option_value","left");
        $this->db->group_by("pro.id");


                if($min_amt!='' && $max_amt!='' && is_array($brand) && is_array($color) && count($brand)>0 && count($color)>0){
                    $this->db->where('pro.product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("pro.brand",$brand);
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt==''&& $max_amt==''&& is_array($brand) && is_array($color)&& (count($brand)>0)&& (count($color)>0)){
                    $this->db->where_in("pro.brand",$brand);
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt==''&& $max_amt==''&& is_array($brand) && (count($brand)>0)){
                    $this->db->where_in("pro.brand",$brand);
                }elseif($min_amt==''&& $max_amt=='' && is_array($color)&& (count($color)>0)){
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt!=''&& $max_amt!='' && is_array($brand)&& (count($brand)>0)){
                    $this->db->where('pro.product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("brand",$brand);
                }elseif($min_amt!=''&& $max_amt!='' && is_array($color)&& (count($color)>0)){
                    $this->db->where('pro.product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                    $this->db->where_in("opt.id",$color);
                }elseif($min_amt!=''&& $max_amt!=''){
                    $this->db->where('pro.product_amount  BETWEEN  "'. $min_amt .'" and "'. $max_amt.'"');
                }elseif($min_amt!=''){
                    $this->db->where('pro.product_amount >=',$min_amt);
                }elseif($max_amt!=''){
                    $this->db->where('pro.product_amount <=',$max_amt);
                }
                $this->db->where("cat.cat_nav", 1);
                $this->db->where("cat.id", $cat_id);
                $res = $this->db->get();

        $i = 0;
        foreach ($res->result() as $row) {
            $option_data = $this->defaultOptionData($row->id);
            $data[$i]['id'] = $row->id;
            $data[$i]['product_name'] = $row->product_name;
            $data[$i]['category'] = $row->category;
            $data[$i]['description'] = $row->description;
            $data[$i]['product_amount'] = $row->product_amount + $option_data['option_amount'];
            $data[$i]['product_pv'] = $row->product_pv;
            $data[$i]['quantity'] = $row->quantity;
            $data[$i]['sort_order'] = $row->sort_order;
            $data[$i]['keyword'] = $row->keyword;
            $data[$i]['files'] = $this->getAllFiles($row->images);
            $data[$i]['brand'] = $this->getBrandName($row->brand);
            $data[$i]['options'] = implode(",",$option_data['option_value']);
            $i ++;
        }
        return $data;
    }
    
    function getProductDtls($pro_id) {
        $data = array();
        $res = $this->db->select("pro.id, product_name, pro.category, pro.description, product_amount, product_pv, quantity, pro.sort_order, pro.keyword, images, bnd.image, bnd.brand_name")
                ->from("products as pro")
                ->join("category as cat", 'cat.id = pro.category', 'inner')
                ->join("brand_settings as bnd", 'bnd.id = pro.brand', 'inner')
                ->where("cat.cat_nav", 1)
                ->where("pro.id", $pro_id)
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $option_data = $this->defaultOptionData($row->id);
            $data[$i]['id'] = $row->id;
            $data[$i]['product_name'] = $row->product_name;
            $data[$i]['category'] = $row->category;
            $data[$i]['description'] = $row->description;
            $data[$i]['product_amount'] = $row->product_amount + $option_data['option_amount'];
            $data[$i]['product_pv'] = $row->product_pv;
            $data[$i]['quantity'] = $row->quantity;
            $data[$i]['sort_order'] = $row->sort_order;
            $data[$i]['keyword'] = $row->keyword;
            $data[$i]['brand_image'] = $row->image;
            $data[$i]['brand_name'] = $row->brand_name;
            $data[$i]['files'] = $this->getAllFiles($row->images);
            $i ++;
        }
        return $data;
    }

    function getProductDetailsView($pro_id) {


        $data = array();
        
        $res = $this->db->select("pro.id, pro.product_name,pro.category,pro.description, pro.product_amount, pro.product_pv, pro.quantity, pro.sort_order,pro.keyword,images,bnd.image")
                ->from("products as pro")
                ->join("brand_settings as bnd", 'bnd.id = pro.brand', 'inner')
                ->where("pro.id", $pro_id)
                ->get();
     
        if ($res->num_rows() >0) {


            $data['id'] = $res->row()->id;
            $data['product_name'] = $res->row()->product_name;
            $data['description'] = $res->row()->description;
            $data['product_amount'] = $res->row()->product_amount;
            $data['product_pv'] = $res->row()->product_pv;
            $data['quantity'] = $res->row()->quantity;
            $data['sort_order'] = $res->row()->sort_order;
            $data['keyword'] = $res->row()->keyword;
            $data['brand_image']= $res->row()->image;
            $data['files'] = $this->getAllFiles($res->row()->images);
 
        }
        return $data;

    }
    
    
    
     function getAllDealOftheDayProducts() {
        $data = array();
        $res = $this->db->select("id,status,product_name,product_amount,product_pv,product_code,product_type,description,brand,images")
                ->from("products")
                ->where("deal_of_the_day", 1)
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $option_data = $this->defaultOptionData($row->id);
            $data[$i]['sl_no'] = $i + 1;
            $data[$i]['id'] = $row->id;
            $data[$i]['product_name'] = $row->product_name;
            $data[$i]['product_amount'] = $row->product_amount + $option_data['option_amount'];
            $data[$i]['product_pv'] = $row->product_pv;
            $data[$i]['product_code'] = $row->product_code;
            $data[$i]['product_type'] = $row->product_type;
            $data[$i]['status'] = $row->status;
            $data[$i]['description'] = $row->description;
            $data[$i]['options'] = implode(",",$option_data['option_value']);
            $data[$i]['files'] = $this->getAllFiles($row->images);
            $data[$i]['brand'] = $this->getBrandName($row->brand);
            $data[$i]['first_image'] = $data[$i]['files'][0]['file_name']??'';

            $i++;
        }
        return $data;
    }
    
    

    function defaultOptionData($pro_id) { 
      
        $data['option_amount'] = 0;
        $data['option_value'] = array();
        $query = $this->db->select('option_values.option_id,option_values.option_value,options.option_name,product_option_values.price,product_option_values.price_change')
                ->join('option_values', 'option_values.id = product_option_values.option_value', 'left')
                ->join('options', 'options.id = product_option_values.option_id', 'left')
                ->where_in('product_option_values.pro_id', $pro_id)
                ->where_in('product_option_values.default_value', 1)
                ->get('product_option_values');

        if ($query->num_rows() > 0) {
          
            foreach ($query->result_array() as $row) {
                if($row['option_name'] != 'Color'){
                    array_push($data['option_value'],$row['option_value']);
                }
                if($row['price_change'] == '+'){
                    $data['option_amount'] += $row['price'];
                }else{
                    $data['option_amount'] -= $row['price'];
                }
            }
        }
        return $data;
    }


       function getAllPopularCategories() {
        $data = array();
        $res = $this->db->select("id,category,image")
                ->from("category")
                ->where('popular_category',1)
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['category'] = $row->category;
            $data[$i]['files'] = $this->getAllFiles($row->image);
            $i ++;
        }
        return $data;
    }

    public function getCatName($cat_id) {
        $name = '';
        $query = $this->db->select('category')
                ->where('id', $cat_id)
                ->limit(1)
                ->get('category');
        if ($query->num_rows() > 0) {
            $name = $query->row()->category;
        }
        return $name;
    }

    function getBrandName($id) {
        $name = '';
        $query = $this->db->select("brand_name")
                ->where('id', $id)
                ->limit(1)
                ->get("brand_settings");
        if ($query->num_rows() > 0) {
            $name = $query->row()->brand_name;
        }
        return $name;
    }

    function getAllPros(){
        
        $data = array();
         $this->db->select("id,status,product_name,product_amount,product_pv,product_code,recurring_type,product_type,description,images,brand,category");
                $this->db->from("products");
                $res =$this->db->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['sl_no'] = $i + 1;
            $data[$i]['id'] = $row->id;
            $data[$i]['product_name'] = $row->product_name;
            $data[$i]['product_amount'] = $row->product_amount;
            $data[$i]['product_pv'] = $row->product_pv;
            $data[$i]['product_code'] = $row->product_code;
            $data[$i]['recurring_type'] = $row->recurring_type;
            $data[$i]['product_type'] = $row->product_type;
            $data[$i]['status'] = $row->status;
            $data[$i]['description'] = $row->description;
            $data[$i]['files'] = $this->getAllFiles($row->images);
            $data[$i]['brand'] = $row->brand;
            $data[$i]['category'] = $row->category;
            $i++;

        }
        return $data;
    }
    
    // Options

    function getOptionLists() {
        $data = array();
        $res = $this->db->select("id, option_name, sort_order")
                ->from("options")
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['option_name'] = $row->option_name;
            $data[$i]['sort_order'] = $row->sort_order;
            $i++;
        }
        return $data;
    }

    function getOptionDetails($id) {
        $data = array();
        $res = $this->db->select("option_name,sort_order")
                ->from("options")
                ->where('id', $id)
                ->limit(1)
                ->get();
        foreach ($res->result() as $row) {
            $data['option_name'] = $row->option_name;
            $data['sort_order'] = $row->sort_order;
        }
        return $data;
    }

    function updateOptions($data) {

        $this->db->set('option_name', $data['option_name'])
                ->set('sort_order', $data['sort_order'])
                ->where('id', $data['update_option'])
                ->update('options');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function addOptionValues($data, $option_id) {
        $this->db->set('option_id', $option_id)
                ->set('option_value', $data['option_value'])
                ->set('sort_order', $data['sort_order'])
                ->insert('option_values');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function deleteOptValues($option_id){
          return $this->db->where('option_id', $option_id)
                ->delete('option_values');

    }

    function getOptionValueLists($option_id="") {
        $data = array();
        $res = $this->db->select("id, option_id, option_value, sort_order")
                ->where('option_id', $option_id)
                ->from("option_values")
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['option_id'] = $row->option_id;
            $data[$i]['option_value'] = $row->option_value;
            $data[$i]['sort_order'] = $row->sort_order;
            $i++;
        }
        return $data;
    }

    function updateOptionValues($data) {

        $this->db->set('option_value', $data['option_value'])
                ->set('sort_order', $data['opt_sort_order'])
                ->where('id', $data['opt_id'])
                ->update('option_values');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function getOptValueDetails($id) {
        $data = array();
        $res = $this->db->select("id,option_id,option_value,sort_order")
                ->from("option_values")
                ->where('id', $id)
                ->limit(1)
                ->get();
        foreach ($res->result() as $row) {
            $data['id'] = $row->id;
            $data['option_id'] = $row->option_id;
            $data['option_value'] = $row->option_value;
            $data['sort_order'] = $row->sort_order;
        }
        return $data;
    }

    function deleteOptionValue($id) {
        $this->db->where('id', $id)
                ->delete('option_values');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return true;
    }

    // Product Options

    function getProductOptions($pro_id="",$option_id = "") {
        $data = array();
        $res = $this->db->select("id,pro_id,option_id,option_value,quantity,price,price_change,default_value")
                ->where("pro_id",$pro_id)
                ->where("option_id",$option_id)
                ->from("product_option_values")
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['pro_id'] = $row->pro_id;
            $data[$i]['option_id'] = $row->option_id;
            $data[$i]['option_name'] = $this->getOptionDetails($row->option_id)['option_name'];
            $data[$i]['option_value'] = $this->getProOptionValue($row->option_value);
            $data[$i]['quantity'] = $row->quantity;
            $data[$i]['price'] = $row->price;
            $data[$i]['price_change'] = $row->price_change;
            $data[$i]['default_value'] = $row->default_value;
            $i++;
        }
        return $data;
    }

    function addProOptionValues($data, $pro_id) {
        $this->db->set('pro_id', $pro_id)
                ->set('option_id', $data['option_ids'])
                ->set('option_value', $data['option_value'])
                ->set('quantity', $data['quantity'])
                ->set('price', $data['price'])
                ->set('price_change', $data['price_change'])
                ->insert('product_option_values');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    public function getProOptionValue($id) {
        $option_value = lang('na');
        $query = $this->db->select('option_value')
                ->where('id', $id)
                ->limit(1)
                ->get('option_values');
        if ($query->num_rows() > 0) {
            $option_value = lang($query->row()->option_value);
        }
        return $option_value;
    }

    function deleteProOptionValue($id) {
        $this->db->where('id', $id)
                ->delete('product_option_values');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return true;
    }

    function updateProOptionValues($data) {
        $this->db->set('option_id', $data['edit_option_id'])
                ->set('option_value', $data['edit_option_value'])
                ->set('quantity', $data['edit_quantity'])
                ->set('price', $data['edit_price'])
                ->set('price_change', $data['edit_price_change'])
                ->where('id', $data['val_id'])
                ->update('product_option_values');
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function getProOptions($id) {
        $data = array();
        $res = $this->db->select("id,pro_id,option_id,option_value,quantity,price,price_change,default_value")
                ->where("id",$id)
                ->from("product_option_values")
                ->get();

        foreach ($res->result() as $row) {
            $data['id'] = $row->id;
            $data['pro_id'] = $row->pro_id;
            $data['option_id'] = $row->option_id;
            $data['option_value'] = $row->option_value;
            $data['quantity'] = $row->quantity;
            $data['price'] = $row->price;
            $data['price_change'] = $row->price_change;
        }
        return $data;
    }

    function getProductOptionData($pro_id) {
       
        $data = array();
        $res = $this->db->select("option_id")
                ->where("pro_id",$pro_id)
                ->group_by("option_id")
                ->get("product_option_values");
        foreach ($res->result_array() as $row) {
            $data[$row['option_id']] = $this->getProductOptions($pro_id,$row['option_id']);
        }
        return $data;
    }
    
    function getAllColors() {
        $data = array();
        $res = $this->db->select("id,option_value")
                ->from("option_values")
                ->where('option_id',3)
                ->get();
        $i = 0;
        foreach ($res->result() as $row) {
            $data[$i]['id'] = $row->id;
            $data[$i]['option_value'] = $row->option_value;
            $i++;
        }
        return $data;
    }

    function updateDefaultValue($id,$pro_id,$option_id) {
       
        $this->db->set('default_value', 1)
                ->where('id', $id)
                ->update('product_option_values');
        if ($this->db->affected_rows() > 0) {
            $this->updateDefaultRadioValue($id,$pro_id,$option_id);

            return true;
        }
        return false;
       
    }

    function updateDefaultRadioValue($id,$pro_id,$option_id) {

        $this->db->set('default_value', 0)
                ->where('pro_id', $pro_id)
                ->where('option_id', $option_id)
                ->where('id !=',$id)
                ->update('product_option_values');
                // echo $this->db->last_query();die;
                // echo $this->db->affected_rows();die;
        if ($this->db->affected_rows() > 0) {
            
            return true;
        }

        return false;
       
    }

    function getOptionValue($id){

        $query = $this->db->select("option_id")
                  ->from("product_option_values")
                  ->where('id', $id)
                  ->get();
       if ($query->num_rows() > 0) {
            $row = $query->row();
            $value = $row->option_id; 
        } 
        return $value;
        
    }

    

}
