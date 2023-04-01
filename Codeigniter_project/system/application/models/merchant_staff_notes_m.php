<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Merchant_staff_notes_m extends MY_Model
{
    protected $table = "crowl_merchant_staff_notes";

  /**
   * Find last staff note (which are viewable only by a store's users).
   * 
   * @author unknown, Christophe
   * @param int $merchant_name_id
   * @param int $store_id
   * @return boolean|object
   */
	public function getLastStaffNoteByMerchantID($merchant_name_id, $store_id) 
	{
    $query_str = "
        SELECT sn.*
		    FROM {$this->table} sn
		    INNER JOIN users_store us ON us.user_id = sn.user_id
		    WHERE sn.merchant_name_id = {$merchant_name_id}
        AND us.store_id = {$store_id}
		    ORDER BY sn.date DESC 	    
		    LIMIT 1
    ";	    
	    
		$query = $this->db->query($query_str);
		
		if ($query->num_rows() == 0)
		{
		    return FALSE;
		}
		
		$row = $query->row_array();
		
		return $row;
	}

	/**
	 * 
	 * @author unknown
	 * @param int $merchant_name_id
	 * @param string $keyword
	 * @param int $limit
	 * @param int $store_id
	 * @return int
	 */
	function getStaffNotesCountByMerchantID($merchant_name_id, $store_id, $keyword = "", $limit = 0) 
	{
		$sql = "
		    SELECT count(sn.id) as note_count 
        FROM {$this->table} sn
		    INNER JOIN users_store us ON us.user_id = sn.user_id
		    WHERE us.store_id = {$store_id}
		";
        
		if ( strlen(trim($keyword)) > 0 ) 
		{
			$keyword = strtolower( trim($keyword) );
			$sql.= " AND lower(sn.entry) like '%".$keyword."%' ";
		}
		
		$sql .= " AND sn.merchant_name_id='".$merchant_name_id."'";
        
		$query = $this->db->query($sql);

		if ($query->num_rows() == 0)
		{
		    return 0;
		}
		
		$row = $query->row_array();
		
		return $row['note_count'];
	}

	/**
	 * 
	 * @author unknown
	 * @param unknown_type $merchant_name_id
	 * @param unknown_type $keyword
	 * @param unknown_type $page
	 * @param unknown_type $page_row
	 * @param unknown_type $store_id
	 */
	function getStaffNotesByMerchantID($merchant_name_id, $store_id, $keyword = "", $page = 0, $page_row = 5) 
	{
		$sql = "
		    SELECT sn.id, sn.user_id, sn.entry, sn.date
        FROM {$this->table} sn
        INNER JOIN users_store us ON us.user_id = sn.user_id 
        WHERE us.store_id = {$store_id}
    ";
        
		if ( strlen(trim($keyword)) > 0 ) 
		{
			$keyword = strtolower(trim($keyword));
			
			$sql.= " AND lower(entry) like '%".$keyword."%'";
		}
		
		$sql .= " AND merchant_name_id='".$merchant_name_id."'";
		$sql .= " ORDER BY `date` desc";
		
		if ( $page_row > 0 ) 
		{
        $sql .= " LIMIT ".($page * $page_row).", ".$page_row;
    }
    
		$query = $this->db->query($sql);

		return $query->result_array();
	}

	function insertStaffNote ( $data ) {
		return $this->db->insert($this->table, $data);
    }

	function updateStaffNote ( $id, $data ) {
		return $this->db->update($this->table, $data, array("id"=>$id));
    }

	function deleteStaffNote ( $id ) {
		return $this->db->where('id', $id)->delete($this->table);
    }

	function deleteStaffNotes ( $ids ) {
		return $this->db->where_in('id', $ids)->delete($this->table);
	}
}