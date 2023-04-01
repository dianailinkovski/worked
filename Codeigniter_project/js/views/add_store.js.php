<script>
$(document).ready(function(){
	$('#create_store_step_1').click(function(){
		if ( $('#store_name').val() == '' ) {
			$('#store_name_error').html('Please enter the store name.');
			return false;
		} else {
			$('#store_name_error').html('');
		}

		return true;
				
	});
});
</script>