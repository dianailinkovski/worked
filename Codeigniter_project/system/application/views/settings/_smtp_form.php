<div id="smtp_message_fb" style="display:none;"></div>
<form id="smtp_settings_form" action="<?=site_url('settings/update_smtp/')?>" method="post">
	<div class="inputBlockContainer">
		<label for="smtp_host">Host:</label>
		<input type="text" id="smtp_host" name="host" value="<?php echo ! empty($smtp['host']) ? $smtp['host'] : '' ?>" placeholder="smtp.example.com" required="required" size="50" maxlength="511" title="The SMTP host URL." />
	</div>
	<div class="inputBlockContainer">
		<label for="smtp_port">Port:</label>
		<input type="number" id="smtp_port" name="port" value="<?php echo ! empty($smtp['port']) ? $smtp['port'] : 25 ?>" required="required" size="6" maxlength="5" title="The SMTP port to use. The default port for SMTP is 25." min="1" max="65535" />
		<label for="smtp_use_ssl">Use SSL:</label>
		<input type="checkbox" id="smtp_use_ssl" name="use_ssl" value="1" onclick="document.getElementById('smtp_use_tls').checked = false;" title="Check if this host requires SSL to send mail." <?php echo (! empty($smtp['use_ssl']) AND $smtp['use_ssl'] == '1') ? 'checked="checked"' : '' ?> />
		<label for="smtp_use_tls">Use TLS:</label>
		<input type="checkbox" id="smtp_use_tls" name="use_tls" value="1" onclick="document.getElementById('smtp_use_ssl').checked = false;" title="Check if this host requires TLS to send mail." <?php echo (! empty($smtp['use_tls']) AND $smtp['use_tls'] == '1') ? 'checked="checked"' : '' ?> />
	</div>
	<div class="inputBlockContainer">
		<label for="smtp_username">Username:</label>
		<input type="text" id="smtp_username" name="username" value="<?php echo ! empty($smtp['username']) ? $smtp['username'] : '' ?>" placeholder="Your SMTP outgoing username" required="required" size="50" maxlength="511" title="The SMTP outgoing username." />
	</div>
	<div class="inputBlockContainer">
		<label for="smtp_password">Password:</label>
		<input type="password" id="smtp_password" name="password" value="<?php echo ! empty($smtp['password']) ? $smtp['fake_password'] : '' ?>" placeholder="Your SMTP outgoing password" required="required" size="50" maxlength="511" title="The SMTP outgoing password." onclick="this.select()" />
	</div>
	<input type="submit" value="Save" name="smtp_save" class="button redButton" />
	<input type="button" value="Remove" name="smtp_remove" id="smtp_remove" class="button redButton" />
</form>
