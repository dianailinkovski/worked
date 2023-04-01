<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	private $rules = array(
		'first_name' => 'trim|required',
		'last_name' => 'trim|required',
		'username' => 'trim|required',
		'password' => 'trim|required',
		'email' => 'trim|required|valid_email',
	);

	function Dashboard()
	{
		parent::__construct();

		$this->load->model('Users_m', 'User');
		$this->load->model("report_m", 'Report');
		$this->load->model("store_m", 'Store');
	}

	function index($id=0)
	{
		echo "index dashboard";exit;
		redirect(base_url()."settings/add_store");
	}

	function save_shortcut()
	{
		$this->data->status = 'error';
		$this->data->html = 'Unable to create shortcut.';
		$this->data->shortcut_url = '';

		$shortcut_url = $_SERVER['HTTP_REFERER'];
		$name = $this->input->post('bookmarkName');
		if($this->store_id && $this->user_id)
		{
			$count = $this->db
				->select('count(*) as count')
				->where('shortcut_name', $name)
				->where('user_id', $this->user_id)
				->get($this->_table_shortcuts)
				->row()->count;

			if( ! $count)
			{
				$data = array(
					'store_id'				 	=> (int)$this->store_id,
					'user_id'       		=> (int)$this->user_id,
					'shortcut_name'     => $name,
					'shortcut_url'      => $shortcut_url,
					'shortcut_add_date' => date('Y-m-d H:i:s')
				);
				if($this->db->insert($this->_table_shortcuts, $data))
				{
					$this->data->html = 'Shortcut "' . $name . '" successfully created.';
					$this->data->status = "success";
					$this->data->shortcut_url = $shortcut_url;
					$this->data->id = $this->db->insert_id();
				}
			}else
			{
				$this->data->html = "Shortcut name already exists.";
			}
		}else
		{
			$this->data->html = "Shortcut name already exists.";
			$this->data->status = "redirect";
		}

		ajax_return($this->data);
	}

	function page($page_name='')
	{
		$this->layout='frontend_inner';
		if($page_name == '')
		{
			redirect(base_url().'dashboard');
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
				redirect(base_url().'dashboard');
			}
		}
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */