<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Merchant_notes_m extends MY_Model
{
	public function getEntriesCountByMerchantNameID ( $merchant_name_id, $keyword="" ) {
		$sql = "SELECT COUNT(*) AS numrows FROM `".$this->_table_crowl_merchant_notes."` WHERE 1=1";
		if ( strlen(trim($keyword)) > 0 ) {
			$keyword = strtolower( trim($keyword) );
			$sql.= " AND (lower(reporter_name) like '%".$keyword."%' OR lower(company) like '%".$keyword."%' OR lower(type_of_entry) like '%".$keyword."%' OR lower(entry) like '%".$keyword."%')";
		}
		$sql .= " AND merchant_name_id='".$merchant_name_id."'";

		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
		return 0;

		$row = $query->row();
		return $row->numrows;
	}

	function getMerchantNameByMerchantNameID ( $merchant_name_id ) {
		$query = $this->db->query("SELECT * FROM ".$this->_table_crowl_merchant_name." WHERE `id`='".$merchant_name_id."'");
		if ($query->num_rows() == 0)
		return FALSE;

		$row = $query->row();
		return $row;
	}

	function searchEntries ( $merchant_name_id, $page=0, $keyword="", $page_row = 5 ) {
		$sql = "SELECT * FROM `".$this->_table_crowl_merchant_notes."` WHERE 1=1";
		if ( strlen(trim($keyword)) > 0 ) {
			$keyword = strtolower( trim($keyword) );
			$sql.= " AND (lower(reporter_name) like '%".$keyword."%' OR lower(company) like '%".$keyword."%' OR lower(type_of_entry) like '%".$keyword."%' OR lower(entry) like '%".$keyword."%')";
		}
		$sql .= " AND merchant_name_id='".$merchant_name_id."'";
		$sql .= " ORDER BY `date` desc";
		if ( $page_row > 0 ) {
			$sql .= " LIMIT ".($page * $page_row).", ".$page_row;
		}

		$query = $this->db->query($sql);

		return $query->result();
	}

	function saveNote ( $merchant_name_id, $data ) {
		$data['merchant_name_id'] = $merchant_name_id;

		return $this->db->insert($this->_table_crowl_merchant_notes, $data);
	}

	function getEntryByID ( $id ) {
		$this->db->select('*');

		if (isset($id)) {
			$this->db->where('id', $id);
		}

		return $this->db->get($this->_table_crowl_merchant_notes)->result();
	}

}