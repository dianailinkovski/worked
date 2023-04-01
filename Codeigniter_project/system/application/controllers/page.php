<?php
class Page extends MY_Controller {
	function Page()
	{		
		parent::__construct();
		$this->layout='frontend';	
	}
	function showPage($page_name='')
	{
		if($page_name == '')
		{
			redirect(base_url().'login');
		}else
		{
			$query = $this->db->get_where('cms_pages', array('type'=>$page_name));
			if($query->num_rows() > 0)
			{
				$results = $query->result();
				$this->data->pages = $results[0];
				$this->data->page_name = $page_name;
				
				$this->load->view('front/merchant/pages', $this->data);
			}else	
			{
				redirect(base_url().'login');
			}
		}
	}
}
