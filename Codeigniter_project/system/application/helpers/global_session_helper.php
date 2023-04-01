<?php



/**
 *
 * function save_new_user
 *
 * @param <array>       $user_info
 *
 */
function save_new_user($user_info) {
	$CI = & get_instance();

	$data = new stdClass();
	$data->user_active = '1';
	$data->user_name = $user_info['first_name'].' '.$user_info['last_name'];
	$data->first_name = $user_info['first_name'];
	$data->last_name = $user_info['last_name'];
	$data->email = $user_info['email'];
	$data->global_user_id = $user_info['id'];

	$ownerInfo = $CI->User->get_global_user($user_info['owner_id']);
	$brands = $CI->Store->get_stores_by_userid($ownerInfo['id']);

	// save user
	$newId = $CI->User->insert((array) $data);
	$data->id = $data->user_id = $CI->db->insert_id();

	//cycle through brands if applicable
	for($i=0, $n=sizeof($brands); $i<$n; $i++){
		$CI->User->add_team_member($brands[$i]->id, $newId);
	}

	return $data;
}