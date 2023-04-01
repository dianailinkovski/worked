<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');
/*****
  * The Pagination helper cuts out some of the bumf of normal pagination
  * @author		Philip Sturgeon
  * @email		email@philsturgeon.co.uk
  * @filename	pagination_helper.php
  * @title		Pagination Helper
  * @version	1.0
  *****/

function create_pagination($uri, $total_rows, $limit = NULL, $uri_segment = 3)
{
	$ci =& get_instance();
	$ci->load->library('pagination');

	$current_page = $ci->uri->segment($uri_segment, 0);
	if($current_page==0){
		if($total_rows < $limit && $total_rows!=0)
			$viewing = '1-'.$total_rows;
		elseif($total_rows!=0)
			$viewing = '1-'.$limit;
		else
			$viewing = '0-0';
	}
	else
	{
		if($total_rows < ($current_page+$limit))
			$viewing = (string)($current_page+1).'-'.(string)($total_rows);
		else
			$viewing = (string)($current_page+1).'-'.(string)($current_page+$limit);
		//$viewing = ($current_page+1).' to '.$current_page+1+$limit;
	}
	// Initialize pagination

        //  Old way: doesn't work without mod_rewrite
        // $config['base_url'] = base_url().$uri.'/';
        // New way: 
    $config['base_url'] = base_url().'/'.$uri.'/';

	$config['total_rows'] = $total_rows; // count all records
	$config['per_page'] = $limit === NULL ? $ci->settings->item('records_per_page') : $limit;
	$config['uri_segment'] = $uri_segment;
	$config['page_query_string'] = FALSE;

	$config['num_links'] = 3;

	$config['full_tag_open'] = '<ul>';
	$config['full_tag_close'] = '</ul>';

	$config['first_link'] = '';//'&lt;&lt;';
	$config['first_tag_open'] = '';
	$config['first_tag_close'] = '';

	$config['prev_link'] = '&laquo;';//'&lt;';
	$config['prev_tag_open'] = '<li>';
	$config['prev_tag_close'] = '</li>';

	$config['cur_tag_open'] = '<li class="selected">';
	$config['cur_tag_close'] = '</li>';

	$config['num_tag_open'] = '<li>';
	$config['num_tag_close'] = '</li>';

	$config['next_link'] = '&raquo;';//'&gt;';
	$config['next_tag_open'] = '<li>';
	$config['next_tag_close'] = '</li>';

	$config['last_link'] = '';//'&gt;&gt;';
	$config['last_tag_open'] = '';
	$config['last_tag_close'] = '';

	$ci->pagination->initialize($config); // initialize pagination

	return array(
		'current_page' 	=> $current_page,
		'per_page' 		=> $config['per_page'],
		'limit'			=> array($config['per_page'], $current_page),
		'links' 		=> $ci->pagination->create_links(),
		'total_rows'	=> $total_rows,
		'viewing'		=> $viewing
	);
}

?>
