<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Trackstreet_data
{
    public function Trackstreet_data() 
    {
        $this->CI =& get_instance();
    }

    /**
     * Compare 2 arrays with one array holding new values that are being updated on a database table 
     * record, and second table holding past values. Detect changes, and then record if a data
     * change was made by a user.
     *  
     * @author Christophe 
     * @param int $user_id
     * @param int $record_id
     * @param string $table
     * @param array $incoming_values
     * @param array $prev_values
     */
    public function record_data_changes($user_id, $record_id, $table, $incoming_values, $prev_values)
    {
        $this->CI->load->model('users_m');
        
        $record_id = intval($record_id);
        
        // don't record changes to these table column names
        $do_not_record = array(
            'created', 'created_at', 'modified', 'modified_at', 'created_by', 'modified_by',
            'updated', 'updated_at'            
        );
        
        foreach ($incoming_values as $key => $incoming_value)
        {
            if (!in_array($key, $do_not_record))
            {            
                if (isset($prev_values[$key]))
                {
                    $prev_value = $prev_values[$key];            
                }
                else
                {
                    $prev_value = '';
                }
                
                if ($incoming_value != $prev_value)
                {
                    $insert_data = array(
                    		'table' => $table,
                    		'record_id' => $record_id, // primary key for table row
                    		'column' => $key,
                    		'prev_value' => $prev_value,
                    		'new_value' => $incoming_value,
                    		'modified_by' => $user_id,
                    		'created' => date('Y-m-d H:i:s'),
                    		'modified' => date('Y-m-d H:i:s')
                    );
                    
                    $this->CI->users_m->insert_data_change($insert_data);
                }
            }
        }
    }
}

?>