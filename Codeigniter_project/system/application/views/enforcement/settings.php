<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Enforcement Settings</strong>
        </h3>        
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview/violation_dashboard">MAP Enforcement</a> 
            <i class="fa fa-angle-right"></i> <a href="/enforcement/settings">Enforcement Settings</a>
        </div>

        <?php if ($this->success_msg != ''): ?>
        	<div class="alert alert-success" role="alert">
        		<?php echo $this->success_msg; ?>
        	</div>
        <?php endif; ?>
        
        <?php if ($this->error_msg != ''): ?>
        	<div class="alert alert-danger" role="alert">
        		<?php echo $this->error_msg; ?>
        	</div>
        <?php endif; ?> 
        
        <?php if (validation_errors() != ''): ?>    
            <div class="alert alert-danger" role="alert">
                <?php echo validation_errors(); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($form_error_msgs)): ?>
            <div class="alert alert-danger" role="alert">
            	<?php foreach ($form_error_msgs as $error_msg): ?>
                    <p><?php echo $error_msg; ?></p>
            	<?php endforeach; ?>
            </div>    
        <?php endif; ?>   
        
        <?php if (!empty($message)): ?> 
    		    <div class="alert alert-success" role="alert">
    		        <?php echo $message; ?>
    		    </div>
    		<?php endif; ?>
    					    
    		<?php if (!empty($error)): ?>
    		    <div class="alert alert-danger" role="alert">
    		        <?php echo $error; ?>
    		    </div>
        <?php endif; ?>         
    
        <div class="form-section-area">
        
            <div class="form-section-area-heading">
                <h3>
                    General Settings
                </h3>
            </div>        
							
						<form action="/enforcement/settings" method="post" id="enforcement_form" class="clear">
						
						    <input type="hidden" name="id" id="id" value="<?=$this->data->id;?>" />
						    
                <?php if (isset($setting_id)): ?>
                    <input type="hidden" name="setting_id" id="setting_id" value="<?=$setting_id;?>" />
                <?php endif; ?>
                
								<h3 class="form-section-subtitle">
								    MAP Enforcement Settings
								</h3>
								
								<!-- Notifications: <?php echo $notifications_on_off; ?> -->
								
								<p>
                    <label for="notifications_on_off">Violation Notifications:</label>
                    <?php echo form_dropdown('notifications_on_off', $notification_options, set_value('notifications_on_off', $notifications_on_off), 'id="notifications-on-off-dropdown"'); ?>
								</p>
								
								<div id="notification-settings-area"<?php if ($notifications_on_off == 'off'): ?> style="display: none;"<?php endif; ?>>
    								
    								<p>
    								    <label for="notification_levels">How many notification levels in your enforcement process?</label>
                        <select name="notification_levels" class="prefill" required="required">
                            <?php 
                            
                            $notification_levels = set_value('notification_levels', $notification_levels);
                            
                            for($num_of_notification_level = 1; $num_of_notification_level <= 10; $num_of_notification_level++)
                            {
                                $selected = $notification_levels==$num_of_notification_level?'selected="selected"':'';
                                
                                echo '<option '.$selected.' value="'.$num_of_notification_level.'">'.$num_of_notification_level.'</option>';
                            }
                            
                            ?>
                        </select>
    								</p>								
    
    								<p>
                        <label for="reset_after_reaching">Reset to first notification level after reaching max notification level?</label>
                        <input type="checkbox" id="reset_after_reaching" name="reset_after_reaching" value="1" title="Check if you would like to have the level reset." <?php echo (! empty($reset_after_reaching) AND $reset_after_reaching == '1') ? 'checked="checked"' : '' ?> />
    								</p>
    
    								<p>
    								    <label for="notification_frequency">How often to send notifications?</label>
                        Every <select name="notification_frequency" class="prefill"  required="required">
                        <?php 
                        
                        $nf = set_value('notification_frequency', $notification_frequency);
    										
                        $freq_list = array(1,2,3,4,5,6,7);
                        
                        foreach ($freq_list as $freq)
                        {
                            $selected = ($nf == $freq or (!$nf && $freq == 2)) ? 'selected="selected"' : '';
                            
                            echo '<option '.$selected.' value="'.$freq.'">'.$freq.'</option>';
                        }
                        
                        ?>
                        </select> days
    								</p> 
    								
    								<p>
    								    <label for="no_notifications_price_in_cart">Consider prices that are under MAP, but only shown after adding a product to cart, as violations?</label>
    								    <input type="checkbox" name="no_notifications_price_in_cart" value="on" <?php echo ($no_notifications_price_in_cart == 'on') ? 'checked="checked"' : '' ?> />
    								</p>
    								
    								<h3 class="form-section-subtitle">
                        Marketplace Notifications
                    </h3>
    								
    								<?php foreach ($marketplaces as $marketplace): ?>
                        <p>
                            <label for="marketplace_notifications_<?php echo $marketplace; ?>">Send violation notifications to <?php echo ucfirst($marketplace); ?> sellers:</label>
                            <?php //echo $marketplace_notification_dropdowns[$marketplace]; ?>
                            <?php echo form_dropdown('marketplace_notifications_' . $marketplace, $notification_options, set_value('marketplace_notifications_' . $marketplace, $marketplace_notification_dropdowns[$marketplace])); ?>
                        </p>
    								<?php endforeach; ?>
    								
                    <h3 class="form-section-subtitle">
                        Email Notification Information
                    </h3>
                    
                    <p>
                        <label for="name_from">Name From:</label>
                    		<input type="text" name="name_from" required="required" id="name_from" class="validate[required,minSize[5],maxSize[255] prefill" value="<?=set_value('name_from', $name_from);?>" maxlength="255">
                    </p>
                    
                    <p>
                        <label for="company">Company Name:</label>
                    		<input type="text" name="company" required="required" id="company" class="medium" value="<?=set_value('company', $company);?>">
                    </p>
                    
                    <p style="margin-bottom: 15px;">
                        <label for="phone">Phone:</label>
                        <input type="text" required="required" name="phone" id="phone" class="prefill" value="<?=set_value('phone', $phone);?>">
                    </p>
                    
                </div>    
                                    
                <h3 class="form-section-subtitle">
                    Your SMTP Settings
                </h3>
                
								<p>
								    <label for="email_from">Email Address:</label>
								    <input type="text" name="email_from" required="required" id="email_from" class="validate[required,email] prefill" value="<?=set_value('email_from', $email_from); ?>" size="50" maxlength="255" title="The e-mail address authorized to send From:">
								</p>
								
                <p>
                    <label for="smtp_host">Host:</label>
                    <input type="text" id="smtp_host" name="smtp_host" value="<?=set_value('smtp_host', $smtp_host);?>" placeholder="smtp.example.com" required="required" size="50" maxlength="511" title="The SMTP host URL." />
                </p>
                
                <p>
                    <label for="smtp_port">Port:</label>
                    <input type="number" id="smtp_port" name="smtp_port" value="<?=set_value('smtp_port', $smtp_port);?>" required="required" size="6" maxlength="5" title="The SMTP port to use. The default port for SMTP is 25." min="1" max="65535" />
                    <label for="smtp_use_ssl">Use SSL:</label>
                    <input type="checkbox" id="smtp_use_ssl" onclick="document.getElementById('smtp_use_tls').checked = false; " name="smtp_ssl" value="YES" title="Check if this host requires SSL to send mail." <?php echo (! empty($smtp_ssl) AND $smtp_ssl == 'YES') ? 'checked="checked"' : '' ?> />
                    <label for="smtp_use_ssl">Use TLS:</label>
                    <input type="checkbox" id="smtp_use_tls" onclick="document.getElementById('smtp_use_ssl').checked = false; " name="smtp_tls" value="YES" title="Check if this host requires TLS to send mail." <?php echo (! empty($smtp_tls) AND $smtp_tls == 'YES') ? 'checked="checked"' : '' ?> />              
                </p>
                
                <p>
                    <label for="smtp_username">Username:</label>
                    <input type="text" id="smtp_username" name="smtp_username" value="<?=set_value('smtp_username', $smtp_username);?>" placeholder="Your SMTP outgoing username" required="required" size="50" maxlength="511" title="The SMTP outgoing username." />
                </p>
                
                <p style="margin-bottom: 15px;">
                    <label for="smtp_password">Password:</label>
                    <input type="password" id="smtp_password" name="smtp_password" value="<?=set_value('smtp_password', $smtp_password);?>" placeholder="Your SMTP outgoing password" required="required" size="50" maxlength="511" title="The SMTP outgoing password." onclick="this.select()" />
                </p>
                
                <hr />
                
                <p>
								    <input name="general_settings_submit" type="submit" value="Save Changes" class="btn btn-success">
								</p>
                
            </form>  
        </div>            
            
        <div class="form-section-area">
        
            <div class="form-section-area-heading">
                <h3>
                    Do Not Sell Settings
                </h3>
            </div>
            
            <form action="/enforcement/settings" method="post" id="dns-settings-form">  
                
                <h3 class="form-section-subtitle">
                    Do Not Sell - General Settings
                </h3>
            
                <p style="margin-bottom:15px;">
                    Do Not Sell list: <?php echo $dns_list_enabled_dropdown; ?>
                </p>
                
                <!-- dns_list_enabled_value_str: <?php echo $dns_list_enabled_value_str; ?> -->
                
                <div id="dns-area-fields"<?php if ($dns_list_enabled_value_str == 'Off'): ?> style="display: none;"<?php endif; ?>>
            
                    <p style="margin-bottom:15px;">
                        Put merchants <?php echo $initial_permanent_dropdown; ?> on DNS List after they rise past notification level <?php echo $notificaton_level_nums_dropdown; ?>.
                    </p>
                    
                    <div id="temporary-settings-area"<?php if ($initial_permanent_setting_value == 1): ?> style="display:none;"<?php endif; ?>>
                    
                        <h3 class="form-section-subtitle">
                            Do Not Sell - Temporary Placement Settings
                        </h3>
                        
                        <p style="margin-bottom:15px;">
                            How many times can a merchant be temporarily placed on the DNS list before they move to permanent DNS status?<br /> 
                            <input style="width:40px;" id="num-of-times-before-perm-field" name="num_of_times_before_perm" value="<?php echo set_value('num_of_times_before_perm', $dns_settings['num_of_times_before_perm']); ?>" type="text" /> Times
                        </p>
                        
                        <div style="margin-bottom:15px;">
                            How many days should violating merchants remain on the DNS List for each time they land on it?<br />
                            <p id="dns-offense-period-times">
                                <?php for ($i = 1; $i <= $num_of_periods_before_perm; $i++): ?>
                                    <span id="offense-time-<?php echo $i; ?>"><input class="small-field" name="remain_on_list_days_offense_<?php echo $i; ?>" value="<?php echo set_value('remain_on_list_days_offense_' . $i, $dns_settings['remain_on_list_days_offense_' . $i]); ?>" type="text" /> days for DNS list entry #<?php echo $i; ?><br /></span>
                                <?php endfor; ?>
                            </p>
                        </div>	        
                        
                    </div> 
                    
                    <h3 class="form-section-subtitle">
                        Do Not Sell - Email Report Settings
                    </h3>   
                    
                    <p>
                        Send a <?php echo $dns_email_report_frequency_dropdown; ?> report about merchants that get added to your DNS list to the following contacts below.
                    </p>
                    
                    <div id="weekly-settings-area"<?php if ($dns_email_report_frequency_setting_value != 'weekly'): ?>style="display:none;"<?php endif; ?>>
                        <p>
                            Day of week to send report: <?php echo $dns_email_report_day_dropdown; ?>
                        </p>
                    </div>    
                    
                    <p>
                        Time for report to be sent: <?php echo $dns_email_report_time_dropdown; ?> Pacific (PST)
                    </p>
                    
                    <h4>
                        Email Subject
                    </h4>
                    
                    <p>
                        <input style="width:500px;" name="email_subject" type="text" value="<?php echo set_value('email_subject', $email_subject); ?>" />
                    </p>
                    
                    <h4>
                        Email Message
                    </h4>
                    
                    <p>
                        <a id="edit-email-template-button" href="#" data-modal-url="/enforcement/do_not_sell_email_template">Edit/View Report Message Template</a>
                    </p>
                    
                    <div id="dns-smtp-settings">
                    
                    </div>
                    
                    <h4>
                        Who Will Receive the Do Not Sell List
                    </h4>
                    
                    <div id="dns-list-people">
                    
                        <div id="dns-team-list">
                            <p>
                                Select Internal Team Members
                            </p>
                            <ul>
                                <?php foreach ($users as $user): ?>
                                    <li class="dns-person">
                                        <div class="dns-person-checkbox">
                                            <input class="checkbox" name="dns_user_ids[]" value="<?php echo $user['id']; ?>" type="checkbox" <?php if (in_array($user['email'], $dns_notify_emails)): ?>checked<?php endif; ?> />
                                        </div>
                                        <div class="dns-person-info">
                                            <h4>
                                                <?php echo $user['first_name']; ?> <?php echo $user['last_name']; ?>
                                            </h4>
                                            <p>
                                                <?php echo $user['email']; ?>
                                            </p>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>    
                        
                        <div id="dns-notify-add-emails">
                        
                            <p>
                                Manage External Receiving Contacts
                            </p>
                        
                            <div id="external-email-addresses">
                                <?php $i = 1; ?>
                                <?php foreach ($dns_notify_external_emails as $row): ?>
                                    <div class="dns-notify-external-area" id="external-email-<?php echo $i ?>">
                                        <div class="dns-notify-external">
                                            <h4>
                                                <?php echo $row['first_name']; ?> <?php echo $row['last_name']; ?>
                                            </h4>
                                            <p>
                                                <?php echo $row['email']; ?>
                                                <!--  
                                                <input style="width: 350px;" placeholder="Email" name="external_email_addresses[]" value="<?php echo $row['email']; ?>" type="text" />
                                                -->
                                            </p>
                                            <input name="external_email_first_names[]" value="<?php echo $row['first_name']; ?>" type="hidden" />
                                            <input name="external_email_last_names[]" value="<?php echo $row['last_name']; ?>" type="hidden" />
                                            <input name="external_email_addresses[]" value="<?php echo $row['email']; ?>" type="hidden" />
                                        </div> 
                                        <a class="remove-external-email-button" data-external-email-id="external-email-<?php echo $i ?>" href="#">Remove</a> 
                                        <div class="clear"></div>      
                                    </div>                         
                                    <?php $i++; ?>      
                                <?php endforeach; ?>                          
                            </div>
                        
                            <p>
                                <a id="add-external-email-button" href="#">+ Add External Contact</a>
                            </p>
                        
                        </div>
                        
                        <div class="clear"></div>                  
                        
                    </div>
                    
                    <h4>
                        Do Not Sell - SMTP Settings
                    </h4>
                    
                    <p>
                        Use global SMTP settings (set above) ? <input type="checkbox" id="dns_use_global_smtp_settings_field" name="dns_use_global_smtp_settings" value="yes" <?php echo ($dns_use_global_smtp_settings == 'yes') ? 'checked="checked"' : '' ?> />
                    </p>
                    
                    <div id="dns-smtp-settings-area"<?php if ($dns_use_global_smtp_settings == 'yes'): ?> style="display:none;"<?php endif; ?>>
                    
                        <p>
        								    <label for="dns_name_from">From Name:</label>
        								    <input type="text" name="dns_name_from" id="dns_name_from" value="<?php echo set_value('dns_name_from', $dns_name_from); ?>" size="50" maxlength="255" title="The name used with From:">
        								</p>
                        
                        <p>
        								    <label for="dns_email_from">From Email Address:</label>
        								    <input type="text" name="dns_email_from" id="dns_email_from" value="<?php echo set_value('dns_email_from', $dns_email_from); ?>" size="50" maxlength="255" title="The e-mail address authorized to send From:">
        								</p>
        								
                        <p>
                            <label for="dns_smtp_host">Host:</label>
                            <input type="text" id="dns_smtp_host" name="dns_smtp_host" value="<?php echo set_value('dns_smtp_host', $dns_smtp_host);?>" placeholder="smtp.example.com" size="50" maxlength="511" title="The SMTP host URL." />
                        </p>
                        
                        <p>
                            <label for="dns_smtp_port">Port:</label>
                            <input type="number" id="dns_smtp_port" name="dns_smtp_port" value="<?php echo set_value('dns_smtp_port', $dns_smtp_port);?>" size="6" maxlength="5" title="The SMTP port to use. The default port for SMTP is 25." min="1" max="65535" />
                            <label for="dns_smtp_use_ssl">Use SSL:</label>
                            <input type="checkbox" id="dns_smtp_use_ssl" onclick="document.getElementById('dns_smtp_use_tls').checked = false; " name="dns_smtp_use_ssl" value="yes" title="Check if this host requires SSL to send mail." <?php echo ($dns_smtp_use_ssl == 'yes') ? 'checked="checked"' : '' ?> />
                            <label for="dns_smtp_use_ssl">Use TLS:</label>
                            <input type="checkbox" id="dns_smtp_use_tls" onclick="document.getElementById('dns_smtp_use_ssl').checked = false; " name="dns_smtp_use_tls" value="yes" title="Check if this host requires TLS to send mail." <?php echo ($dns_smtp_use_tls == 'yes') ? 'checked="checked"' : '' ?> />              
                        </p>
                        
                        <p>
                            <label for="dns_smtp_username">Username:</label>
                            <input type="text" id="dns_smtp_username" name="dns_smtp_username" value="<?php echo set_value('dns_smtp_username', $dns_smtp_username); ?>" placeholder="Your SMTP outgoing username" size="50" maxlength="511" title="The SMTP outgoing username." />
                        </p>
                        
                        <p style="margin-bottom: 15px;">
                            <label for="dns_smtp_password">Password:</label>
                            <input type="password" id="dns_smtp_password" name="dns_smtp_password" value="<?php echo set_value('dns_smtp_password', $dns_smtp_password); ?>" placeholder="Your SMTP outgoing password" size="50" maxlength="511" title="The SMTP outgoing password." onclick="this.select()" />
                        </p>
                        
                    </div>        
                
                </div>
                
                <hr />                
                                     
								<p>
								    <input name="dns_settings_submit" type="submit" value="Save Changes" class="btn btn-success">
								</p>
								
				    </form>
		    </div>
		    
		    <!--  
		    <div>
		        <?php var_dump($_POST); ?>
		    </div>
		    -->		    

    </div>
</div>  

<!-- start modal -->
<div class="modal fade" id="dynamic-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <div class="hidden-xs">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Modal title</h4>
        </div>
        <div class="hidden-sm hidden-md hidden-lg sml-header">
          <button type="button" class="close sml-txt" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title sml-txt" id="myModalLabel">Modal title</h4>
        </div>
      </div>
      
      <div class="modal-body">
          <iframe width="100%" height="100%" style="width: 100%; height: 100%;" id="modal-iframe" src=""></iframe>   
      </div>
      
    </div>
  </div>
</div>
<!-- end modal -->

<script type="text/javascript">

var offense_time_count = <?php echo $num_of_periods_before_perm; ?>;	
var external_email_count = <?php echo count($dns_notify_external_emails); ?>

function add_offense_time_row() 
{
    offense_time_count++;
    
    // add new offense period form field
    var append_content = 
    '<span id="offense-time-' + offense_time_count + '">' + 
    '<input class="small-field" name="remain_on_list_days_offense_' + offense_time_count + 
    '" value="30" type="text" /> days for DNS list entry #' + offense_time_count + '<br /></span>';
    
    $('#dns-offense-period-times').append(append_content);	
}


      
$(document).ready(function() {

    $('#add-offense-dns-time-button').click(function() {

        add_offense_time_row() 
        
        return false;
    });

    $('#remove-offense-dns-time-button').click(function() {

        if (confirm('Remove offense list time?'))
        {
            $('#offense-time-' + offense_time_count).remove();
    	
            offense_time_count--;
        }

        return false;
        
    });

    $('#add-external-email-button').click(function() {

        external_email_count++;	
        
        var append_content = 
            '<div class="dns-notify-external-area" id="external-email-' + external_email_count + '">' + 
                '<div class="dns-notify-external">' +
                    '<p class="dns-notify-external-field">' +
                        '<input style="width: 200px;" placeholder="First Name" name="external_email_first_names[]" value="" type="text" />' +
                    '</p>' +    
                    '<p class="dns-notify-external-field">' +
                        '<input style="width: 200px;" placeholder="Last Name" name="external_email_last_names[]" value="" type="text" />' +
                    '</p>' +
                    '<p class="dns-notify-external-field">' +
                        '<input style="width: 200px;" placeholder="Email" name="external_email_addresses[]" value="" type="text" />' +
                    '</p>' +
                '</div>' + 
                '<a class="remove-external-email-button" data-external-email-id="external-email-' + external_email_count + '" href="#">Remove</a>' +  
                '<div class="clear"></div>' +      
            '</div>';
        
        $('#external-email-addresses').append(append_content);
        
        return false;
    });    

    $('#initial-permanent-dropdown').on('change', function() {
        
        $('#temporary-settings-area').toggle();
        
    });

    $('#dns-email-report-frequency-dropdown').on('change', function() {
        
        $('#weekly-settings-area').toggle();
        
    });

    $('#dns-list-enabled-dropdown').on('change', function() {
        
        $('#dns-area-fields').toggle();
        
    });

    $('#dns_use_global_smtp_settings_field').on('change', function() {
        
        $('#dns-smtp-settings-area').toggle();
        
    });

    $('#notifications-on-off-dropdown').on('change', function() {
        
        $('#notification-settings-area').toggle();
        
    });
    
    $('#num-of-times-before-perm-field').on('change', function() {
        
        //alert($('#num-of-times-before-perm-field').val());
        
        var orig_offense_time_count = parseInt(offense_time_count);
        var num_of_times_before_perm = parseInt($('#num-of-times-before-perm-field').val());

        if (num_of_times_before_perm > 0 && num_of_times_before_perm < 11)
        {
            offense_time_count = 0;

            $('#dns-offense-period-times').html('');
            
            for (var i = 0; i < num_of_times_before_perm; i++)
            {
                add_offense_time_row();
            }
        }
        else
        {
            alert('Error: Please enter a value that is between 1 and 10 with this field.');

            $('#num-of-times-before-perm-field').val(orig_offense_time_count);
        }
        
    });

    $(document).on("click", ".remove-external-email-button", function(){

        var remove_elem_id = $(this).attr('data-external-email-id');
        
        $('#' + remove_elem_id).remove();

        return false;
        
    });

    $('#edit-email-template-button').click(function(){

        data_changed = false;

        $('#dynamic-modal').modal('show');

        var modal_iframe_src = $(this).attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Edit DNS List Email Report Template');

        return false;
    	
    });
	
});

</script>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55db664540eb9" data-unique="55db664540eb9" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55db664540eb9"></script>
<?php endif; ?>    