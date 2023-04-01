<script type="text/javascript">
jQuery(document).ready(function(){
	$('#addStore1').submit(function(event) {
		return ValidateAddStore();
	});

	$('#smtp_port').on('input', function(){
		$this = $(this);
		if(this.value.length > 5)
			$this.val(this.value.slice(0,5))
		if(this.value < $this.attr('min'))
			$this.val($this.attr('min'));
		else if (this.value > $this.attr('max'))
			$this.val($this.attr('max'));
	});
	
	var $smtp_form = $('#smtp_settings_form');
	$smtp_form.on('submit', function(e){
		e.preventDefault();
		submit_smtp_settings($smtp_form, false);
	});
	$('#smtp_remove').on('click', function(e){
		submit_smtp_settings($smtp_form, true);
	});
});

/**
 * Submit the SMTP settings form via AJAX
 * and pass the returned JSON data to
 * submit_smtp_settings_cb
 *
 * @param FormElement $form
 */
function submit_smtp_settings($form, remove){
	// hide the current messages
	$('#smtp_messages_fb').hide('slow');

	// validate the form
	var host = $('#smtp_host').val();
	var port = $('#smtp_port').val();
	var username = $('#smtp_username').val();
	var password = $('#smtp_password').val();

	var errors = [];
	if (empty(host))
		errors.push('Please enter an SMTP host.');
	if (empty(port) || port < 1 || port > 65535)
		errors.push('Please enter the SMTP port between 1 and 65535.');
	if (empty(username))
		errors.push('Please enter the SMTP username.');
	if (empty(password))
		errors.push('Please enter the SMTP password.');

	if (errors.length > 0) {
		var error = '<p>' + errors.join('</p><p>') + '</p>';
		sv('#notify_message_fb').set_error(error);

		return false;
	}

	// everything is valid, let's post it
	$.post(
		$form.attr('action') + (remove ? '/remove' : ''),
		$form.serialize(),
		function(response){ submit_smtp_settings_cb(response) },
		'json'
	);
}

/**
 * Handle the AJAX response from submit_smtp_settings
 *
 * @param JSON data
 */
function submit_smtp_settings_cb(response){
	var msg_id = '#smtp_message_fb';
	if (response.status)
		sv(msg_id).set_success(response.html, 'slow');
	else
		sv(msg_id).set_error(response.html, 'slow');

	if (response.remove) {
		$('#smtp_host').val('');
		$('#smtp_port').val(25);
		$('#smtp_username').val('');
		$('#smtp_password').val('');
		$('#smtp_use_ssl').prop('checked', '');
	}

	setTimeout(function(){
		$(msg_id).hide('slow');
	}, 5000);
}

function ValidateAddStore(){
	//not currently using the manufacturer ID
  return (ValidateStoreName()/*  && ValidateManID() */)
}
function ValidateStoreName(){
  var name = $('#store_name').val();
  if(name.length < 3){
    inlineMsg('store_name','<strong>Error</strong><br />Brand name must be greater then or equal to 3 characters in length.',2);
  }else if(name.length > 255){
    inlineMsg('store_name','<strong>Error</strong><br />Brand name must be less then or equal to 255 characters in length.',2);
  }else if(name==''){
    inlineMsg('store_name','<strong>Error</strong><br />Please enter a brand name.',2);
  }else{
    return true;
  }
  return false;
}
function ValidateManID(){
  var id = $('#man_id').val();
  var reg  =/[\D]/;
  if(id != '' && (id.length < 5 || id.length > 9 || reg.test(id))){
    inlineMsg('man_id','<strong>Error</strong><br />Manufacturer ID is a number 5-9 digits in length.',2);
  }else{
		return true;
	}
	return false;
}

function inlineMsg(msg) {
	$("#brand_logo_error").html(msg).show();
}
</script>