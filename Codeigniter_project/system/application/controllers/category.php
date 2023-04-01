<?php

class Category extends MY_Controller
{
    private $admin_perms = false;
	
	function __construct()
    {
        parent::__construct();
		$this->load->model("Category_m", "Category");
		$this->load->model('marketplace_m', 'Marketplace');
		$this->load->model('store_m', 'Store');
		$this->javascript('views/categories.js.php');
		$this->data->BASE_URL = $this->config->item('base_url');
		
		// TODO: security
		if(isset($this->permission_id) && ($this->permission_id == '0' || $this->permission_id == '2')){
			$this->admin_perms = true;
		}
    }
    
    // TODO: something more "codeignter-y" than this?
    private function _view_assign($vars){
        foreach ($vars as $key => $value){
			$this->data->$key = $value;
        }
    }
    
    function index(){
		if(!$this->admin_perms) return;
        redirect($this->data->BASE_URL."category/show");
    }
    
    /** DISPLAY FUNCTIONS START HERE::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::/
     */
    function show($params='')
    {
		if(!$this->admin_perms) return;
        parse_str($params, $params);
        $catId = (isset($params['catId']) && ($params['catId'] > 0)) ? $params['catId'] : false;
		$params['do'] = $this->input->post('do');
        if (isset($params['do']) && $params['do'] == "save") {
			$params['name'] = $this->input->post('name');
			$params['parentid'] = $this->input->post('parentid');
            $res = $this->Category->save($params);
            if ($res && !empty($res['errors'])) {
                $vars['ERRORS'] = $res['errors'];
            } else {
                $vars['SUCCESS'] = "Category {$params['name']} is saved.";
            }
        }
        
        $parentid                  = ($catId) ? $catId : -1;
		if($parentid==-1){
			$vars['SELECT_CATEGORIES'] = $this->Category->get_categories(array('catId'=>$catId));
			$vars['MENU']              = $this->Category->generate_option_html($vars['SELECT_CATEGORIES'][0]['parentid'] ,$vars['SELECT_CATEGORIES']);
		}
        $vars['CATEGORIES']        = $this->Category->get_simple_categories('sub', $parentid, 'ASC');
        $vars['BREADCRUMBS']       = $this->Category->get_breadcrumb($catId);
        $vars['CATEGORY']          = $this->Category->get_category($catId);
        
        $this->_view_assign($vars);
    }
    
    function ulist($params='')
    {
        parse_str($params, $params);
        $catId = (isset($params['catId']) && ($params['catId'] > 0)) ? $params['catId'] : false;
        
        $parentid                  = ($catId) ? $catId : -1;
        $vars['SELECT_CATEGORIES'] = $this->Category->get_categories(array('catId'=>$catId));
        $vars['LIST']              = $this->Category->generate_list_html($vars['SELECT_CATEGORIES'][0]['parentid'] ,$vars['SELECT_CATEGORIES']);
        
        $this->_view_assign($vars);
    }
    
    function move($params)
    {
		if(!$this->admin_perms) return;
        parse_str($params, $params);
        (isset($params['srcCatId']) && ($params['srcCatId'] > 0)) ? $srcCatId = $params['srcCatId'] : $srcCatId = false;
        
		$params['do'] = $this->input->post('do');
        if (isset($params['do']) && $params['do'] == "save") {
			$params['srcCatId'] = $this->input->post('srcCatId');
			$params['dstCatId'] = $this->input->post('dstCatId');
            $res = $this->Category->move_data($params);
            if (!empty($res['errors'])) {
                $vars['ERRORS'] = $res['errors'];
            } else {
                $vars['SUCCESS'] = "Category was moved successfully";
            }
        }
        $vars['SELECT_CATEGORIES'] = $this->Category->get_categories();
        $vars['MENU']              = $this->Category->generate_option_html($vars['SELECT_CATEGORIES'][0]['parentid'] ,$vars['SELECT_CATEGORIES']);
        $vars['BREADCRUMBS']       = $this->Category->get_breadcrumb($srcCatId);
        $vars['CATEGORY']          = $this->Category->get_category($srcCatId);
        
        $this->_view_assign($vars);   
    }
	
	function crawlers($params){
		if(!$this->admin_perms) return;
        parse_str($params, $params);
        $catId = (isset($params['catId']) && ($params['catId'] > 0)) ? $params['catId'] : false;
		$params['do'] = $this->input->post('do');
        if (isset($params['do']) && $params['do'] == "save") {
            $res = $this->Category->save_categories_marketplaces($catId, $this->input->post('crawlerIds'));
            if (!empty($res['errors'])) {
                $vars['ERRORS'] = $res['errors'];
            } else {
                $vars['SUCCESS'] = "Crawler / Category data saved";
            }
        }
		$vars['CAT_MRKTS']		   = $this->Category->get_marketplace_ids_by_category_id($catId);
		$vars['CATEGORY']          = $this->Category->get_category($catId);
		$vars['MARKETPLACES']      = $this->Marketplace->get_marketplaces('*');

        $this->_view_assign($vars);   
	}
	
    
    function delete($params)
    {
		if(!$this->admin_perms) return;
        parse_str($params, $params2);
        if (isset($params2['catId'])) {
            $this->Category->delete($params2['catId']);
        }
        redirect($this->data->BASE_URL."category/show");
    }
	
	
	function store($params){
        parse_str($params, $params);
        $storeId = (isset($params['storeId']) && ($params['storeId'] > 0)) ? $params['storeId'] : false;
		$params['do'] = $this->input->post('do');
        if (isset($params['do']) && $params['do'] == "save") {
            $res = $this->Category->save_categories_stores($storeId, $this->input->post('catIds'));
            if (!empty($res['errors'])) {
                $vars['ERRORS'] = $res['errors'];
            } else {
                $vars['SUCCESS'] = "Store / Category data saved";
            }
        }
		$vars['STORE']             = $this->Store->get_store_info($storeId);
        $vars['SELECT_CATEGORIES'] = $this->Category->get_categories();//array('catId'=>$catId)
        $vars['LIST']              = $this->Category->generate_list_html($vars['SELECT_CATEGORIES'][0]['parentid'] ,$vars['SELECT_CATEGORIES'], 'with checkboxes');
		$vars['CHECKED_VALUES']	   = $this->Category->get_category_ids_by_storeId($storeId);
		
        $this->_view_assign($vars);   
	}
    
}
?>