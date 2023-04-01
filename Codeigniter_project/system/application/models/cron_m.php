<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cron_m extends MY_Model 
{
    /**
     * Insert a new row into cron_run_log table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_cron_run_log($insert_data)
    {
    	$this->db->insert('cron_run_log', $insert_data);
    
    	return $this->db->insert_id();
    }  
}

?>