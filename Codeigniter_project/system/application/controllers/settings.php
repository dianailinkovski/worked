<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller 
{
    public $record_per_page;
    public $file_upload_error;
	
    public function __construct()
    {
    		parent::__construct();
    
    		$this->load->library('form_validation');
    
    		$this->load->model('account_m', "Account");
    		$this->load->model("Users_m", 'User');
    		$this->load->model("products_m", 'Product');
    		$this->load->model("report_m", 'Report');
    		$this->load->model("store_m", 'Store');
    		
    		$this->javascript('views/add_store.js.php');
    }

    public function index()
    {
        $this->javascript('views/settings.js.php');
        $this->data->new_store_id = $this->data->message = '';
    }
    
    /**
     * Form where users can add a new store (brand).
     * 
     * @author unknown, Christophe
     */
    public function add_store()
    {
        $this->javascript('views/settings.js.php');
        
        $this->data->new_store_id = $this->data->message = '';
        
        $this->_view = $this->_controller . '/index';
    }
	
    /**
     * Form where user can edit details for store.
     * 
     * @author unknown, Christophe
     */	
    public function edit_store()
    {
        $store_info = array();
        
        $rules = array(
        		array(
        				'field' => 'edit_store_name',
        				'label' => 'Store Name',
        				'rules' => 'trim|required'
        		),
        		array(
        				'field' => 'brand_name_variants',
        				'label' => 'Brand Name Variants',
        				'rules' => 'trim'
        		),
        		array(
        				'field' => 'man_id',
        				'label' => 'Manufacturer ID',
        				'rules' => 'trim|numeric|min_length[5]|max_length[9]'
        		)
        );
        
        $this->form_validation->set_rules($rules);
        $this->form_validation->set_fields();
        
        if (empty($store)) $store = new stdClass;
        
        foreach (array_keys($rules) as $field)
        {
            $store->$field = set_value($field);
        }
        
        $this->data->merchant_logo = $this->Account->get_merchant_thumb($this->store_id);
        
        if ($this->input->post('edit_store_name'))
        {
        	$this->data->store_name = $this->input->post("edit_store_name");
        	$this->data->man_id = $this->input->post('man_id');
        	$this->data->note_enable = $this->input->post('note_enable');
        	$this->data->brand_name_variants = $this->input->post('brand_name_variants');
        
        	if ($this->form_validation->run())
        	{
        		$store_info['store_name'] = $this->data->store_name;
        		$store_info['brand_name_variants'] = $this->data->brand_name_variants;
        		$store_info['man_id'] = $this->data->man_id;
        		$store_info['id'] = $this->store_id;
        		$store_info['note_enable'] = $this->data->note_enable;
        		$this->Store->update_store_info($store_info);
        		
        		if ($_FILES['brand_logo']['error'] != 4)
        		{
        			$upload = $this->upload_logo();
        			
        			if (!$upload)
        			{
        				$this->data->store_name = $store_name;
        				$this->data->man_id = $man_id;
        				$this->data->message = $this->file_upload_error;
        				
        				redirect(base_url().'enforcement');
        			}
        			else
        			{
        				$this->db->where('id', $this->store_id);
        				$this->db->update($this->_table_store, array('brand_logo' => $this->uploaded_file));
        				$this->data->merchant_logo = $this->Account->get_merchant_thumb($this->store_id);
        
        				$old_image = $this->input->post('old_image');
        				
        				if ($old_image != '')
        				{
        					$this->load->library('S3');
        					
        					$s3 = new S3($this->config->item('s3_access_key'), $this->config->item('s3_secret_key'));
        					
        					$s3Folder = 'stickyvision/brand_logos/';
        					
        					$s3->deleteObject($this->config->item('s3_bucket_name'), $s3Folder.$old_image);
        					$s3->deleteObject($this->config->item('s3_bucket_name'), $s3Folder.str_replace('_thumb', '', $old_image));
        					
        					unset($s3);
        				}
        				
        				$this->data->message = 'Brand information successfully updated.';
        			}
        		}
        		else
        		{
        			$this->data->message = 'Brand information successfully updated.';
        		}
        	}
        	else
        	{
        		$this->data->message = 'Invalid Brand Name or Manufacturer ID.';
        	}
        } 
        else 
        {
        	$this->data->store_id = $this->store_id;
        	
        	$info = $this->Store->get_store_info($this->store_id);
        
        	$this->data->man_id = isset($info['man_id']) ? $info['man_id'] : '';
        	$this->data->store_name = isset($info['store_name']) ? $info['store_name'] : 'All Brands';
        	$this->data->note_enable = isset($info['note_enable']) ? $info['note_enable'] : '0';
        	$this->data->brand_name_variants = isset($info['brand_name_variants']) ? $info['brand_name_variants'] : '';
        }
        
        $this->javascript('views/settings.js.php');        
    }

    /**
    * Handle form submission of add store (brand) form.
    * 
    * @author unknown, Christophe
    */
    public function add_brand()
    {
        $this->load->library('Vision_users');
        
        $rules = array(
            array(
                'field' => 'store_name',
                'label' => 'Store Name',
                'rules' => 'trim|required'
            ),
            array(
                'field' => 'man_id',
                'label' => 'Manufacturer ID',
                'rules' => 'trim|numeric|min_length[5]|max_length[9]'
            )
        );

        $this->form_validation->set_rules($rules);
        $this->form_validation->set_fields();

        $store_name = $this->input->post('store_name');
        $man_id = $this->input->post('man_id');
        
        // this is to handle failed file uploads so as not to duplicated the store name
        $new_store_id = $this->input->post('new_store_id');

        if ($this->form_validation->run())
        {
            $store_info['user_id'] = $this->user_id;
            $store_info['store_name'] = $store_name;
            $store_info['store_enable'] = '1';
            $store_info['created_at'] = date("Y-m-d H:i:s");
            $store_info['man_id']= $man_id;

            if (!empty($new_store_id))
            {
                // if store already exists, then connect user to store
                $store_info['id'] = $new_store_id;
                
                $this->store_id = $new_store_id;
                
                $this->Store->update_store_info($store_info);
                
                // send notification to TrackStreet staff about store being added
                $email = $this->config->item('environment') == 'production' ? 'christophe@trackstreet.com, chris@trackstreet.com' : 'christophe@trackstreet.com';
                 
                $subject = '[TrackStreet] New Store Added: ' . $store_name;
                 
                $html_message = "<p>A new store (brand) has been added: {$store_name} (ID: {$this->store_id})</p>";
                $html_message .= "<p>User who added store: {$this->user['first_name']} {$this->user['last_name']} (ID: {$this->user_id})</p>";
                
                $text_message = strip_tags($html_message);
                 
                $this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);                
            }
            else
            {                
                // create new store
                $this->store_id = $this->Store->add_store($store_info);
                 
                $this->User->add_team_member($this->store_id, $this->user_id);
                
                // send notification to TrackStreet staff about new store being added
                $email = $this->config->item('environment') == 'production' ? 'christophe@trackstreet.com, chris@trackstreet.com' : 'christophe@trackstreet.com';
                 
                $subject = '[TrackStreet] New Store Added: ' . $store_name;
                 
                $html_message = "<p>A new store (brand) has been added: {$store_name} (ID: {$this->store_id})</p>";
                $html_message .= "<p>User who added store: {$this->user['first_name']} {$this->user['last_name']} (ID: {$this->user_id})</p>";
                                
                $text_message = strip_tags($html_message);
                 
                $this->vision_users->sendSESEmail($email, $subject, $html_message, $text_message);
                 
                parent::_switch_brand($this->store_id);
            }

            $this->store_id = $this->data->new_store_id = $this->store_id;

            if ($_FILES['brand_logo']['error'] != 4)
            {
                $upload = $this->upload_logo();
                
                if (!$upload)
                {
                    $this->data->store_name = $store_name;
                    $this->data->man_id = $man_id;
                    $this->data->message = $this->file_upload_error;
                    
                    $this->session->set_flashdata('error_msg', 'Error: Logo could not be uploaded. Please try again, or contact support for help.');
                    
                    redirect('/settings/add_store');
                    exit();
                }
                else
                {
                    $this->db->where('id', $this->store_id);
                    $this->db->update($this->_table_store, array('brand_logo' => $this->uploaded_file));
                    
                    redirect(base_url() . 'settings/products');
                }
            }
            else
            {
                redirect(base_url() . 'settings/products');
            }
        }
        else
        {
            $this->data->store_name = $store_name;
            $this->data->man_id = $man_id;
            $this->data->new_store_id = '';
            $this->data->message = validation_errors();
        }

        $this->javascript('views/settings.js.php');
        
        $this->_view = $this->_controller . '/index';
    }

	function upload_logo(){

		if($_FILES['brand_logo']['error'] == 0){
			//upload and update the file
			$iT = time();
			$logo_name = $this->store_id.'_logo_'.$iT.'.'.$this->getFileExtension($_FILES['brand_logo']['name']);
			$thumb_name = $this->store_id.'_logo_'.$iT.'_thumb.'.$this->getFileExtension($_FILES['brand_logo']['name']);

			$config['upload_path'] = $this->config->item('uploaded_files').'brand_logo_images/';
            if(!is_dir($this->config->item('uploaded_files').'brand_logo_images/')) {
                chmod($this->config->item('uploaded_files'), 0777);
                mkdir($this->config->item('uploaded_files').'brand_logo_images/', 0777, true);
                chmod($this->config->item('uploaded_files').'brand_logo_images/', 0777);
            }
			$config['allowed_types'] = 'gif|jpg|png';
			$config['overwrite'] = true;
			$config['remove_spaces'] = true;
			$config['file_name'] = $logo_name;
			$this->load->library('upload', $config);

			if(!$this->upload->do_upload('brand_logo')){
				$this->file_upload_error = $this->upload->display_errors('', '');
				return false;
			}else{
				//Image Resizing
				$config['image_library'] = 'gd2';
				$config['source_image'] = $this->config->item('uploaded_files').'brand_logo_images/'.$logo_name;
				$config['maintain_ratio'] = TRUE;
				$config['create_thumb'] = TRUE;
				$config['width'] = 200;
				$config['height'] = 64;

				$this->load->library('image_lib', $config);
				$this->image_lib->resize();
				if(!$this->image_lib->resize()){
					$upload_error = $this->image_lib->display_errors();
					$this->image_lib->clear();
					$this->file_upload_error = $upload_error;
					@unlink($config['source_image'].$logo_name);
					return false;
				}else{
					$has_error = false;
					$this->load->library('S3');
					$s3 = new S3($this->config->item('s3_access_key'), $this->config->item('s3_secret_key'));
					$s3Folder = 'stickyvision/brand_logos/';
					if(file_exists($config['upload_path'].$logo_name)) {
						if($put = $s3->putObjectFile($config['upload_path'].$logo_name, $this->config->item('s3_bucket_name'), $s3Folder.$logo_name, S3::ACL_PUBLIC_READ)) {
						}else{
							$has_error = true;
							$this->file_upload_error = 'Could not upload logo to server.';
						}
						@unlink($config['upload_path'].$logo_name);
					}
					if(file_exists($config['upload_path'].$thumb_name)) {
						if($s3->putObjectFile($config['upload_path'].$thumb_name, $this->config->item('s3_bucket_name'), $s3Folder.$thumb_name, S3::ACL_PUBLIC_READ)) {
						}else{
							$has_error = true;
							$this->file_upload_error = 'Could not upload thumbnail to server.';
						}
						@unlink($config['upload_path'].$thumb_name);
					}
					unset($s3);

					$this->uploaded_file = $logo_name;

					return $has_error ? false : true;
				}
			}
		}
	}

    private function getFileExtension($filename)
    {
        $array = explode('.', $filename);
        
        return end($array);
    }

    /**
     * Page where users can add new products.
     * 
     * @author unknown, Christophe
     */
    public function products()
    {
        if (!$this->store_id)
        {
            $this->session->set_flashdata('error_msg', 'Error: Store not selected.');
            
            redirect('/');
        }
        
        if ($this->role_id != 2)
        {
            $this->session->set_flashdata('error_msg', 'Error: Your account does not have access to this item.');
            
            redirect('/');
            exit();
        }

        $this->javascript('views/add_products.js.php');
        $this->data->error = '';
        $this->data->product_count = 1;
        $this->data->product_method = $this->input->post('product_method') == FALSE ? 'bycsv' : $this->input->post('product_method');
        $this->data->file_processing = false;
        //var_dump($this->data->product_method); exit();
        
        $this->data->title = $this->input->post('title');
        $this->data->upc_code = $this->input->post('upc_code');
        $this->data->sku = $this->input->post('sku');
        $this->data->retail_price = $this->input->post('retail_price');
        $this->data->wholesale_price = $this->input->post('wholesale_price');
        $this->data->price_floor = $this->input->post('price_floor');
        
        if ($this->input->post('product_method') == 'byform')
        {
            // this will be an array of x products...
        
            $this->data->product_count = sizeof($this->data->title);
            
            $rules = array(
            	array(
            		'field' => 'title[]',
            		'label' => 'Title',
            		'rules' => 'trim|required'
            	),
            	array(
            		'field' => 'upc_code[]',
            		'label' => 'UPC Code',
            		'rules' => 'trim|required'
            	),
            	array(
            		'field' => 'retail_price[]',
            		'label' => 'Retail Price',
            		'rules' => 'trim|required'
            	),
            	array(
            		'field' => 'price_floor[]',
            		'label' => 'Price Floor',
            		'rules' => 'trim|required'
            	)
            );
        
            $this->form_validation->set_rules($rules);
            $this->form_validation->set_fields();
            
            if ($this->form_validation->run())
            {
        	      for($i = 0; $i < $this->data->product_count; $i++)
        	      {
                    $existing_product_id = $this->Product->check_product_exist($this->store_id, $this->data->upc_code[$i], $this->data->sku[$i]);
                  
        			      if (!$existing_product_id)
        			      {
                        $data = array(
                        	'store_id' => $this->store_id,
                        	'title' => $this->data->title[$i],
                        	'upc_code' => $this->data->upc_code[$i],
                        	'brand' => '',
                        	'sku' => $this->data->sku[$i],
                        	'retail_price' => $this->data->retail_price[$i],
                        	'price_floor' => $this->data->price_floor[$i],
                        	'is_tracked' => 1,
                        	'created_at' => date('Y-m-d H:i:s'),
                        	'wholesale_price' => $this->data->wholesale_price[$i],
                        	'status' => 1
                        );
                        
        				        $newProdId = $this->Product->add_product($data);
        				
        				        $iT = time();
        
                        if (isset($data['retail_price']))
                        {
                            $this->db->insert($this->_table_products_pricing, array('product_id' => $newProdId,'pricing_type' => 'retail_price','pricing_value' => $data['retail_price'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
                        }
                        
                        if (isset($data['wholesale_price']))
                        {
                            $this->db->insert($this->_table_products_pricing, array('product_id' => $newProdId,'pricing_type' => 'wholesale_price','pricing_value' => $data['wholesale_price'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
                        }
                        
                        if (isset($data['price_floor']))
                        {
                            $this->db->insert($this->_table_products_pricing, array('product_id' => $newProdId,'pricing_type' => 'price_floor','pricing_value' => $data['price_floor'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
                        }
                    }
        			      else 
        			      {
                        $data = array(
                        	'title' => $this->data->title[$i],
                        	'upc_code' => $this->data->upc_code[$i],
                        	'brand' => '',
                        	'sku' => $this->data->sku[$i],
                        	'retail_price' => $this->data->retail_price[$i],
                        	'price_floor' => $this->data->price_floor[$i],
                        	'is_tracked' => 1,
                        	'wholesale_price' => $this->data->wholesale_price[$i],
                        	'created_at' => date('Y-m-d H:i:s'),
                        	'status' => 1
                        );
                        
        				        $this->Product->update_product($existing_product_id,$data);
        				
        				        $newProdId = $existing_product_id;
        				
        				        $iT = time();
        				
        				        if (isset($data['retail_price']))
        				        {
        					          $this->db->where('product_id',$newProdId);
                            $this->db->where('pricing_type','retail_price');
                            $this->db->update($this->_table_products_pricing, array('pricing_value' => $data['retail_price'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
        				        }
        
        				        if (isset($data['wholesale_price']))
        				        {
        					          $this->db->where('product_id',$newProdId);
                            $this->db->where('pricing_type','wholesale_price');
                            $this->db->update($this->_table_products_pricing, array('pricing_value' => $data['wholesale_price'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
        				        }
        
        				        if (isset($data['price_floor']))
        				        {
                            $this->db->where('product_id',$newProdId);
                            $this->db->where('pricing_type','price_floor');
                            $this->db->update($this->_table_products_pricing, array('pricing_value' => $data['price_floor'],'pricing_start' => date('Y-m-d H:i:s', $iT)));
        				        }
        			      }
        	      }
        
        		    redirect(base_url().'catalog');
            }
            else
            {
                //we're going back to the form to fix error...
                $this->data->error = 'There is an error with one or more of your products. The title, UPC, Retail &amp; MAP price values are required.';
            }
        } 
        elseif ($this->input->post('product_method') == 'bycsv')
        {
            if ($_FILES['csv_file']['tmp_name'])
            {
        		    $config['upload_path'] = $this->config->item('uploaded_files').'brand_csv_uploads/'.$this->store_id;
        		
        		    $brand_file = $this->config->item('uploaded_files').'brand_csv_uploads';
        		
        		    $config['allowed_types'] = 'csv';
        		    $config['overwrite'] = true;
        		    $config['remove_spaces'] = true;
        		
        		    //$config['file_name'] = $_FILES['csv_file']['name'];
        		    //if(!is_dir($brand_file)) mkdir($brand_file, 0777);
        		    if (!is_dir($config['upload_path']))
        		    {
                    mkdir($config['upload_path'], 0777, true);
        			      chmod($config['upload_path'], 0777);
        		    }
        
        		    $this->load->library('upload', $config);
        
        		    if ($this->upload->do_upload('csv_file'))
        		    {
        			      //we've posted a file
                    ini_set("auto_detect_line_endings", "1");
        			
                    $this->data->file_name = $this->upload->file_name;//$config['file_name'];
        			      $this->data->file_processing = true;
        
        			      $dataArray = $headerArray = array();
        			
        			      $has_header = $this->input->post("has_header");
        			      
        			      // Does user want to archive all existing products in system that are NOT contained in this CSV file?
        			      $archive_products = $this->input->post("archive_products");
        			
        			      $this->data->has_header = $has_header;
        			      $this->data->archive_products = $archive_products;
        			      $this->data->headerColumns = array();
        
        			      $handle = fopen($config['upload_path'] . '/' . $this->data->file_name, "r");
        
        			      while (($data = fgetcsv($handle, 9999999, ",")) !== FALSE)
        			      {
        				        $dataArray[] = $data;
        				
        				        if (count($data) > count($this->data->headerColumns))
        				        {
        					          $this->data->headerColumns = $data;
        				        }
        			      }
        			
        			      fclose($handle);
        
        			      if ($this->data->has_header)
        			      {
        				        if (isset($dataArray[0]))
        				        {
        					          if (count($dataArray[0]) < count($this->data->headerColumns))
        					          {
        						            $missing_indexes = count($this->data->headerColumns) - count($dataArray[0]);
        						            
        						            $this->data->headerColumns = array_merge($dataArray[0], array_fill(count($dataArray[0]), $missing_indexes, ''));
        					          }
        					          else	
        					          {
        						            $this->data->headerColumns = $dataArray[0];
        					          }
        				        }
        
        				        $var_to_match = '';
        				
        				        for ($i = 0, $n = count($this->data->headerColumns); $i < $n; $i++)
        				        {
        					          if ($n - 1 == $i)
        					          {
        						            $var_to_match .= $i.'=>%~'.preg_replace('/[=>%~&]/', '', $this->data->headerColumns[$i]);
        					          }
        					          else
        					          {
        						            $var_to_match .= $i.'=>%~'.preg_replace('/[=>%~&]/', '', $this->data->headerColumns[$i]).'&';
        					          }
        				        }    
        				
        				        $headerArray = generateHeaderArray($var_to_match);
        				
        				        if ($headerArray != '')
        				        {
        					          $headerArray = generateHeaderPostArray($headerArray);
        				        }
        				        else
        				        {
        					          $headerArray = generateHeaderPostArray($var_to_match);
        				        }
        			      }
        			      
                    $this->data->dataArray = $dataArray;
                    $this->data->headerArray = $headerArray;
                    
                    $this->javascript('views/import.js.php');
                }
                else
                {
        			      $this->data->error = $this->upload->display_errors();
        		    }
            }
        	  else
            {
        		    $this->data->error = 'No file was uploaded.';
        	  }
        }
    }

    /**
     * Once the user has gone through and matched the column values to their CSV file,
     * they click the save button and this controller file will handle either adding or updating
     * the product data.
     * 
     * @author unknown, Christophe
     */
    public function save_csv_products()
    {
        $this->load->library('Vision_products');
        $this->load->library('Trackstreet_products');
        $this->load->model('products_m');
        $this->load->model('products_pricing_m');
        
        // see: http://stackoverflow.com/questions/4541749/fgetcsv-fails-to-read-line-ending-in-mac-formatted-csv-file-any-better-solution
        // could not initially import ASP store file
        ini_set("auto_detect_line_endings", TRUE);
        
        $parse_file = $this->input->post('file_name');
        $has_header = $this->input->post('has_header');
        $cols_count = $this->input->post('cols_count');
        $archive_products = $this->input->post('archive_products');
        
        //headerArray = system, headerColumns = user provided
        $headerArray = $headerColumns = $dataArray = array();
        
        //echo "<pre>";
        //print($this->config->item('uploaded_files').'brand_csv_uploads/'.$this->store_id.'/'.$parse_file); 
        
        // CSV files are saved here and are not deleted after this completes in case we need them in the future
        $handle = fopen($this->config->item('uploaded_files') . 'brand_csv_uploads/' . $this->store_id . '/' . $parse_file, "r");
        
        while (($data = fgetcsv($handle, 9999999, ",")) !== FALSE)
        {            
            $dataArray[] = $data;
        }
        
        //var_dump($dataArray); exit();
        
        fclose($handle);
        
        //get columns defined by user
        for ($i = 0, $n = sizeof($dataArray[0]); $i < $n; $i++)
        {
            $headerArray[$i] = $this->input->post('header_' . $i);
        }
        
        if ($has_header)
        {
            $headerColumns = $dataArray[0];
        	
            array_shift($dataArray);
        }
        
        //var_dump($dataArray); exit();
        
        $added_products = array();
        $updated_products = array();
        
        // keep track of the IDs for the products contained in CSV file
        $csv_file_product_ids = array();
        
        // check to make sure UPC codes are unique to each product
        $bad_upcs = $this->trackstreet_products->bad_upc_check($dataArray, $headerArray);
        
        if ($bad_upcs !== FALSE)
        {
            $this->session->set_flashdata(
                'error_msg', 
                '<p>We detected that 1 or more UPC codes are used with more than one product. ' . 
                'Please correct your CSV file and retry the import process.</p>' . 
                '<p>Bad UPCs: ' . implode(', ', $bad_upcs) . '</p>'            
            );
            
            redirect('/settings/products');
            exit();
        }
        
        for ($i = 0; $i < count($dataArray); $i++)
        {
            $data = array_combine($headerArray, $dataArray[$i]);
            $data = array_map('trim', $data);
            
            if (isset($data['wholesale_price'])) 
            {
                // remove $,",',commas
                $data['wholesale_price'] = trim($data['wholesale_price'], '$');
                $data['wholesale_price'] = str_replace('"', '', $data['wholesale_price']);
                $data['wholesale_price'] = str_replace("'", '', $data['wholesale_price']);
                $data['wholesale_price'] = str_replace(",", '', $data['wholesale_price']);
            }
            
            if (isset($data['retail_price'])) 
            {
                // remove $,",',commas
                $data['retail_price'] = trim($data['retail_price'], '$');
                $data['retail_price'] = str_replace('"', '', $data['retail_price']);
                $data['retail_price'] = str_replace("'", '', $data['retail_price']);  
                $data['retail_price'] = str_replace(",", '', $data['retail_price']);
            }
            
            if (isset($data['price_floor'])) 
            {
                // remove $,",',commas
                $data['price_floor'] = trim($data['price_floor'], '$');
                $data['price_floor'] = str_replace('"', '', $data['price_floor']);
                $data['price_floor'] = str_replace("'", '', $data['price_floor']);
                $data['price_floor'] = str_replace(",", '', $data['price_floor']);
            }
            
            unset($data['ignore']);
            
            $data['store_id'] = $this->store_id;
            $data['is_tracked'] = 1; // make sure product is tracked
            $data['is_archived'] = 0; // make sure product is not archived
            $data['status'] = 1;
            $sku = isset($data['sku']) ? $data['sku'] : '';
            
            // skip rows in CSV file that are blank
            if ($data['upc_code'] == '' || $data['upc_code'] == FALSE || $data['title'] == '' || $data['title'] == FALSE)
            {
                log_message('debug', 'save_csv_products() - missing UPC or title - $i = ' . $i);
                
                // skip 
                continue;
            }
            
            //var_dump($data); exit();
            
            // modified this function as we don't need SKU - Christophe 12/1/2015
            // sometimes customers weren't providing SKU so they were uploading duplicate products
            $existing_product_id = $this->Product->check_product_exist($this->store_id, $data['upc_code']);
            
            $existing_product_id = intval($existing_product_id);
            
            log_message('debug', 'save_csv_products() - $existing_product_id: ' . $existing_product_id);
            
            if (!$existing_product_id)
            {                
                $data['created_at'] = date("Y-m-d h:i:s");
                
                $newProdId = $this->Product->add_product($data);
                
                log_message('debug', 'save_csv_products() - added new product - $newProdId: ' . $newProdId);
                
                $product = $this->Product->get_product_by_id($newProdId);
                
                $iT = time();
                
                if (isset($data['retail_price']))
                {
                    $insert_data = array(
                        'product_id' => $newProdId,
                        'pricing_type' => 'retail_price',
                        'pricing_value' => $data['retail_price'],
                        'pricing_start' => date('Y-m-d H:i:s', $iT)
                    );
                    
                    $this->products_pricing_m->insert($insert_data);
                    
                    $product['retail_price'] = $data['retail_price'];
                }
                
                if (isset($data['wholesale_price']))
                {
                    $insert_data = array(
                        'product_id' => $newProdId,
                        'pricing_type' => 'wholesale_price',
                        'pricing_value' => $data['wholesale_price'],
                        'pricing_start' => date('Y-m-d H:i:s', $iT)
                    );
                    
                    $this->products_pricing_m->insert($insert_data);
                    
                    $product['wholesale_price'] = $data['wholesale_price'];
                }
                
                if (isset($data['price_floor']))
                {
                    $insert_data = array(
                        'product_id' => $newProdId,
                        'pricing_type' => 'price_floor',
                        'pricing_value' => $data['price_floor'],
                        'pricing_start' => date('Y-m-d H:i:s', $iT)
                    );
                    
                    $this->products_pricing_m->insert($insert_data);
                    
                    $product['price_floor'] = $data['price_floor'];
                }
                
                $newProdId = intval($newProdId);
                
                if (in_array($newProdId, $csv_file_product_ids) == FALSE)
                {
                    // add product ID to CSV file list
                    $csv_file_product_ids[] = $newProdId;
                }
                
                $added_products[] = $product; 
            }
            else 
            {
                $existing_product_id = intval($existing_product_id);
                
                log_message('debug', 'save_csv_products() - updating existing product: ' . $existing_product_id);
                
                // we assume that user wants all products in CSV file to be active - Christophe 11/13/2015
                $data['is_tracked'] = 1;
                $data['status'] = 1;
                
                // update existing product with data from CSV file
                $this->Product->update_product($existing_product_id, $data);
                
                if (in_array($existing_product_id, $csv_file_product_ids) == FALSE)
                {
                    // add product ID to CSV file list
                    $csv_file_product_ids[] = $existing_product_id;
                }
                
                $newProdId = $existing_product_id;
                
                $product = $this->Product->get_product_by_id($newProdId);
                
                $iT = time();
                
                if (isset($data['retail_price']))
                {
                    $update_data = array('pricing_value' => $data['retail_price'], 'pricing_start' => date('Y-m-d H:i:s', $iT));
                    
                    $this->products_pricing_m->update_pricing_by_product($newProdId, 'retail_price', $update_data);
                    
                    $product['retail_price'] = $data['retail_price'];
                }
                
                if (isset($data['wholesale_price']))
                {
                    $update_data = array('pricing_value' => $data['wholesale_price'],'pricing_start' => date('Y-m-d H:i:s', $iT));
                    
                    $this->products_pricing_m->update_pricing_by_product($newProdId, 'wholesale_price', $update_data);
                    
                    $product['wholesale_price'] = $data['wholesale_price'];
                }
                
                if (isset($data['price_floor']))
                {
                    $update_data = array('pricing_value' => $data['price_floor'],'pricing_start' => date('Y-m-d H:i:s', $iT));
                    
                    $this->products_pricing_m->update_pricing_by_product($newProdId, 'price_floor', $update_data);
                    
                    $product['price_floor'] = $data['price_floor'];
                }
                
                $updated_products[] = $product;
            }
        }
        
        $archive_product_ids = array();
        
        // if user checked box to archive existing products that were NOT in CSV file, do that action next
        if ($archive_products)
        {   
            // go through all products and archive all that are not in $csv_file_product_ids
            $products = $this->products_m->get_all_products_for_store($this->store_id);
            
            for ($i = 0; $i < count($products); $i++)
            {
                $product_id = intval($products[$i]['id']);
            
                if (in_array($product_id, $csv_file_product_ids) == FALSE)
                {
                    if (in_array($product_id, $archive_product_ids) == FALSE)
                    {
                        // if existing product was not in CSV file, then archive
                        $archive_product_ids[] = $product_id;
                    }
                }
            }
            
            $this->vision_products->archive_products($archive_product_ids, $this->store_id);
        }        
        
        // old:
        //redirect(base_url() . 'catalog');
        
        // show details on the data that was imported
        $this->data->added_products = $added_products;
        $this->data->updated_products = $updated_products;
        $this->data->csv_file_product_ids = $csv_file_product_ids;
        $this->data->archive_product_ids = $archive_product_ids;
        $this->data->debug_mode = FALSE;
        $this->data->success_msg = 'Import of product data successfully completed.';
	}

	/**
	 * Handle the AJAX request to update or remove SMTP settings
	 */
	public function update_smtp($remove = FALSE)
	{
		$this->_response_type('json');

		// If the update is to remove the record that is all we need to do
		if ($remove) {
			if ($this->data->status = $this->data->remove = $this->_remove_smtp())
				$this->data->html = 'Your SMTP settings have been removed.';
			else
				$this->data->html = 'Your SMTP settings could not be removed.';

			return;
		}

		// Otherwise, update the record
		$fields = getTableFields($this->_table_store_smtp, array('id', 'store_id'));
		$data = $this->input->post($fields);
		$data['password'] = $this->input->post_orig('password', FALSE); // get this value unfiltered
		$data['store_id'] = $this->store_id;

		// validate the data
		$errors = array();
		if ( ! $this->_validate_smtp($data['host'], $data['port'], $data['use_ssl'], $data['use_tls']))
			$errors[] = $data['host'] . ':' . $data['port'] . ' is not a valid URL. Please check that the host and port are correct.';

		if ( ! empty($errors)) {
			$this->data->html = '<p>' . implode('</p><p>', $errors) . '</p>' ;
			return;
		}

		// Check if the password contains our mask
		// If it doesn't, then it has been updated
		$fake_password_mask = str_repeat('*', 512);
		if (strpos($data['password'], $fake_password_mask) !== FALSE)
			unset($data['password']);

		// add to database
		$this->data->status = $this->Store->set_store_smtp($data['store_id'], $data);
		if ($this->data->status)
			$this->data->html = 'Your SMTP settings have been updated.';
		else
			$this->data->html = 'Your SMTP settings could not be updated.';
	}

	/**
	 * Handle the AJAX request to remove SMTP settings
	 * @return type
	 */
	private function _remove_smtp()
	{
		return $this->Store->delete_store_smtp($this->store_id);
	}

	private function _validate_smtp($host, $port, $use_ssl = FALSE, $use_tls = FALSE)
	{
		$url = $host . ':' . $port;
		if ($use_ssl)
			$url = 'ssl://' . $url;

		if ($use_tls)
			$url = 'tls://' . $url;

		$valid = filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
		if ( ! $valid) // FILTER_VALIDATE_URL requires a protocol, any protocol
			$valid = filter_var('smtp://' . $url);

		// Also check for a top level domain
		$parts = explode('.', $host);
		$valid = ($valid AND count($parts) >= 2);

		return $valid;
	}

}
