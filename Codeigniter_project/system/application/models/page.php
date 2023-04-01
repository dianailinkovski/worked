<?php 

class Page extends Model
{
	function Pages()
    {
        parent::Model();
    }	
	
	function getPages()
	{
		return $this->db->get('pages')->result();	
	}
	function insertPage()
	{
		$data = array('title'	=> $this->input->post('title'),
					  'meta_keywords'=>	$this->input->post('meta_keywords'),
					  'meta_description'=>	$this->input->post('meta_description'),
					  'body'=>	$this->input->post('body'),
					  'url'=>	$this->input->post('url'),
					  'created' => date('Y-m-d')
		
					 );	
			$this->db->insert('pages',$data);		 
		
	}
	function updatePage()
	{
				$id = $this->input->post('id');
				$data = array('title'	=> $this->input->post('title'),
					  'meta_keywords'=>	$this->input->post('meta_keywords'),
					  'meta_description'=>	$this->input->post('meta_description'),
					  'body'=>	$this->input->post('body'),
					  'url'=>	$this->input->post('url'),
					  'created' => date('Y-m-d')
		
					 );	
				
				$this->db->where('id', $id);
				$this->db->update('pages', $data);	
		
	}
	
	function editPage($id)
	{
		return $this->db->get_where('pages',array('id'=>$id))->result();	
	}
}
?>