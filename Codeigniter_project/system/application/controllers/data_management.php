<?php

class Data_management extends MY_Controller {

	function Data_management() {

		parent::__construct();

		$this->load->library('form_validation');
		$this->load->model("report_m", 'Report');
		$this->load->model('products_m', 'Items');
		$this->load->model('store_m', 'Store');
		$this->load->model('upc_m', 'UPC');
	}

	function saveCsvData($type=0, $contain_header=0, $start_index=0) {

		if ($this->input->post('cols_count')) {
			$dataArray = array();
			$dataIndexes = array();
			$dataValues = array();
			$data_save = array();
			$json_decoded_array = array();
			$data_row_error = array();
			$headerArray = array();
			$data_csv_save = "";
			$cols_count = $this->input->post('cols_count');
			$rows_count = $this->input->post('rows_count');
			$hasHeader = $this->input->post('hasHeader');
			$rowStart = $hasHeader ? 1 : 0;
			$json_decoded_array = json_decode($this->input->post('json_array'));
			$json_decoded_array = $json_decoded_array->myArray;

			// Prep csv data
			$this->data->headerColumns = array();

			for ($i = $rowStart; $i < $rows_count; $i++) {
				for ($j = 0; $j < $cols_count; $j++) {
					$dataArray[$i][$j] = $json_decoded_array[$i][$j];
				}

				if (count($json_decoded_array[$i]) > count($this->data->headerColumns)) {
					$this->data->headerColumns = array_fill(0, count($json_decoded_array[$i]), '');
				}
			}

			for ($i = 0; $i < $cols_count; $i++) {
				$name = 'hid_'.$i;
				if (isset($_POST[$name])) {
					$dataIndexes[] = $i;
					$dataValues[] = $_POST[$name];
				}
				if ($hasHeader) {
					$name = 'hid_'.$i;
					$post_index = "header_0_".$i;
					if (isset($_POST[$post_index])) {
						$headerArray[0][$i] = $_POST[$post_index];
					}
					if (isset($_POST[$name])) {
						$headerArray[1][$i] = $_POST[$name];
					}
				} else {
					if (isset($_POST[$name])) {
						$headerArray[1][$i] = $_POST[$name];
						$headerArray[0][$i] = $_POST[$name];
					} else {
						$headerArray[1][$i] = '';
						$headerArray[0][$i] = '';
					}
				}
			}

			for ($i = 0, $n = count($headerArray[0]); $i < $n; $i++) {
				$val = "";
				if (isset($headerArray[1][$i])) {
					$val = $headerArray[1][$i];
				} else {
					$val = "";
				}
				if ($i == ($n - 1))
					$data_csv_save .= $i."=>".$val."~".preg_replace('/[=>%~&]/', '', $headerArray[0][$i]);
				else
					$data_csv_save .= $i."=>".$val."~".preg_replace('/[=>%~&]/', '', $headerArray[0][$i])."&";
			}
			for ($j = $rowStart; $j < $rows_count; $j++) {
				for ($i = 0, $m = count($dataIndexes); $i < $m; $i++) {
					$data_save[$j]['store_id'] = $this->store_id;
					if ($dataValues[$i] == 'upc_code') {
						if (is_numeric(preg_replace('/[^0-9]/', '', $dataArray[$j][$dataIndexes[$i]])))
							$data_save[$j][$dataValues[$i]] = htmlentities(round(preg_replace('/[^0-9]/', '', $dataArray[$j][$dataIndexes[$i]])));
						else {
							if (is_numeric(preg_replace('/[^0-9]/', '', $dataArray[$j][$dataIndexes[$i]]))) {
								$data_save[$dataValues[$i]] = htmlentities(round(preg_replace('/[^0-9]/', '', $dataArray[$j][$dataIndexes[$i]])));
								$data_save[$j]['is_tracked'] = 1;
								$data_save[$j]['status'] = 1;
							}else {
								$this->data->error = 'Please select correct UPC.';
								$data_row_error[] = $j;
								$data_save[$j]['is_tracked'] = 0;
								$data_save[$j]['status'] = 0;
							}
						}
					} elseif ($dataValues[$i] == 'price_floor' || $dataValues[$i] == 'retail_price' || $dataValues[$i] == 'wholesale_price') {
						$priceWithoutDollar = trim(str_replace('$', '', $dataArray[$j][$dataIndexes[$i]]));
						if (!is_numeric($priceWithoutDollar)) $priceWithoutDollar = '';
						if ($priceWithoutDollar == '' || is_numeric($priceWithoutDollar)) {
							if ($priceWithoutDollar == '')
								$priceWithoutDollar = 0.00;

							$data_save[$j][$dataValues[$i]] = round($priceWithoutDollar, 2);
						}
						else {
							$this->data->error = 'Please select correct numeric value for prices.';
							$data_row_error[] = $j;
							$data_save[$j]['is_tracked'] = 0;
							$data_save[$j]['status'] = 0;
						}
					}
					else
						$data_save[$j][$dataValues[$i]] = htmlentities($dataArray[$j][$dataIndexes[$i]]);
				}
			}

			// start saving products
			$total_saved = $total_not_saved = 0;
			$errors = array();
			$brand = $this->Store->get_brand_by_store($this->store_id);

			if (isset($headerArray[1]) && in_array('upc_code', $headerArray[1])) {

				for ($i = $rowStart; $i < $rows_count; $i++) {
					if (isset($data_save[$i]['upc_code'])) {
						$data_save[$i]['brand'] = $brand;
						$data_save[$i]['created_at'] = date('Y-m-d H:i:s');
						if ($this->Items->product_exists_by_upc($data_save[$i]['upc_code'])) {
							$product_id = $this->Items->get_product_id_from_upc($data_save[$i]['upc_code']);
							if (!is_null($product_id)) {
								$newValues = array();
								if (isset($data_save[$i]['title']) and $data_save[$i]['title'] != '') $newValues['title'] = $data_save[$i]['title'];
								if (isset($data_save[$i]['sku']) and $data_save[$i]['sku'] != '') $newValues['sku'] = $data_save[$i]['sku'];

								if (sizeof($newValues) > 0) {
									$this->Items->update($product_id, $newValues);
								}

								$pricing_start = date('Y-m-d h:i:s');
								foreach (array('wholesale_price', 'retail_price', 'price_floor') as $pricing_type) {
									if (!isset($data_save[$i][$pricing_type]) && $data_save[$i][$pricing_type] == '' && !is_numeric($data_save[$i][$pricing_type])) {
										continue;
									}
									$this->db->insert(
										'products_pricing',
										array(
											'product_id' => $product_id,
											'pricing_type' => $pricing_type,
											'pricing_value' => $data_save[$i][$pricing_type],
											'pricing_start' => $pricing_start
										)
									);
								}
								$total_saved++;
								log_message('success', 'Product updated failed: '.print_r($data_save[$i], true));
							}else {
								$total_not_saved++;
								$errors[] = $data_save[$i]['upc_code'].' is already in our database but was unable to be updated.';
								log_message('error', $data_save[$i]['upc_code'].' is already in our database but was unable to be updated.');
							}
						} elseif ($this->db->insert('products', $data_save[$i])) {
							$product_id = mysql_insert_id();
							$pricing_start = date('Y-m-d h:i:s');
							foreach (array('wholesale_price', 'retail_price', 'price_floor') as $pricing_type) {
								$this->db->insert(
									'products_pricing',
									array(
										'product_id' => $product_id,
										'pricing_type' => $pricing_type,
										'pricing_value' => $data_save[$i][$pricing_type],
										'pricing_start' => $pricing_start
									)
								);
							}
							$total_saved++;
						} else {
							log_message('error', 'Product insert failed: '.print_r($data_save[$i], true));
						}
					}
				}

				if (count($errors)) {
					$this->session->set_flashdata('error', implode("\n<br>", $errors));
				}
			}else {
				if ($hasHeader) {
					$dataArray[0] = array_fill(0, count($this->data->headerColumns), '');
					for ($j = 0; $j < $cols_count; $j++) {
						$post_name = "header_0_".$j;
						$dataArray[0][$j] = $this->input->post($post_name);
					}

					$this->data->headerColumns = $dataArray[0];
				}elseif (isset($headerArray[1])) {
					$this->data->headerColumns = $headerArray[1];
				}

				$this->data->dataArray = $dataArray;
				$headerArray = generateHeaderPostArray($data_csv_save);
				$this->data->headerArray = $headerArray;
				$this->data->filename = 'filename';
				$this->data->hasHeader = $hasHeader ? 1 : 0;
				$this->data->error = 'Please select UPC column to import data.';
				$this->load->view('front/merchant/import', $this->data);
			}

			$this->saveDefaultCSVSettings($data_csv_save);
			$this->session->set_flashdata("products_saved", $total_saved);
			$this->session->set_flashdata("products_not_saved", $total_not_saved);
			if ($total_saved > 0) {
				$this->session->set_flashdata("success", 'File import success.');
			}elseif ($total_not_saved === 0) {
				$this->session->set_flashdata("error", 'File import failed.');
			}
		}

		redirect(base_url().'catalog/');
	}

	function saveDefaultCSVSettings($dataToSave) {
		$saved_data = $this->db->get_where('csv_default_columns', array('array_values' => $dataToSave))->result();
		if (count($saved_data) == 0) {
			$this->db->insert('csv_default_columns', array('array_values' => $dataToSave, 'add_date_time' => date('Y-m-d H:i:s')));
		}
	}

}