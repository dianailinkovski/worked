<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

/**
 * Model to interact with old Accounts DB.
 * 
 * Note: Delete when we switch to new auth system.
 * 
 * @author csautot
 */
class accounts_db_app_terms extends Model 
{

	public function __construct()
	{
		parent::Model();

		$this->adb = $this->load->database('accounts_db', TRUE);
	}
	
	/**
	 * Fetch all user records from Accounts db user table.
	 * 
	 * @author Christophe
	 */
	public function get_vision_terms()
	{
	    $this->adb->select('AT.*');
	    $this->adb->from('application_terms AT');
	    $this->adb->where('app_id', 3);
	    
	    $query = $this->adb->get();
	    
	    return $query->result_array();	    
	}
}

?>