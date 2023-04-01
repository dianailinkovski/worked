<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

/**
 * Model to interact with old Accounts DB.
 * 
 * Note: Delete when we switch to new auth system.
 * 
 * @author csautot
 */
class accounts_db_users extends Model 
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
	public function get_users()
	{
	    $this->adb->select('U.*');
	    $this->adb->from('users U');
	    
	    $query = $this->adb->get();
	    
	    return $query->result_array();	    
	}
	
	/**
	 * Find a single team record based on user (member) ID.
	 * 
	 * @author Christophe
	 * @param int $user_id
	 */
	public function get_team_record($user_id)
	{
	    $this->adb->select('UT.*');
	    $this->adb->from('users_teams UT');
	     
	    $query = $this->adb->get();
	     
	    return $query->row_array();	    
	}
}

?>