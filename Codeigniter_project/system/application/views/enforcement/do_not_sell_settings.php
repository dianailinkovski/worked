<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Do Not Sell List - Settings</strong>
        </h3>        
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview/violation_dashboard">MAP Enforcement</a> 
            <i class="fa fa-angle-right"></i> Do Not Sell List
            <i class="fa fa-angle-right"></i> Settings
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
        
        <form action="/enforcement/do_not_sell_settings" method="post">
        
            <h3 class="form-section-heading">
                General Settings
            </h3>
        
            <p style="margin-bottom:15px;">
                Do Not Sell list: <?php echo $dns_list_enabled_dropdown; ?>
            </p>
        
            <p style="margin-bottom:15px;">
                Put merchants <?php echo $initial_permanent_dropdown; ?> on DNS List after they rise past notification level <?php echo $notificaton_level_nums_dropdown; ?>.
            </p>
            
            <div id="temporary-settings-area"<?php if ($initial_permanent_setting_value == 1): ?> style="display:none;"<?php endif; ?>>
            
                <h3 class="form-section-heading">
                    Temporary Placement Settings
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
                    <!--          
                    <p>
                        <a id="add-offense-dns-time-button" href="#">+ Add Repeat Offense List Time</a><br />
                        <a id="remove-offense-dns-time-button" href="#">- Remove Repeat Offense List Time</a>
                    </p>
                    -->
                </div>	        
                
            </div> 
            
            <h3 class="form-section-heading">
                Email Report Settings
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
                Time for report to be sent: <?php echo $dns_email_report_time_dropdown; ?> PST
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
                Receiving Contacts
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
                                    <p>
                                        <input placeholder="First Name" name="external_email_first_names[]" value="<?php echo $row['first_name']; ?>" type="text" />
                                        <input placeholder="Last Name" name="external_email_last_names[]" value="<?php echo $row['last_name']; ?>" type="text" />
                                    </p>
                                    <p>
                                        <input style="width: 350px;" placeholder="Email" name="external_email_addresses[]" value="<?php echo $row['email']; ?>" type="text" />
                                    </p>
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
            
            <hr />
            
            <p style="margin-top: 20px;">                
                <input class="btn btn-success" type="submit" value="Save Changes" />
            </p>            
        
        </form>    

    </div>
</div>

<!-- start modal -->
<div class="sent-notice-model modal fade" id="dynamic-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modal-title">
            Modal Window Title                                     
        </h4>
      </div>
      
      <iframe width="100%" height="720" id="modal-iframe" src=""></iframe>    
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                    '<p>' +
                        '<input placeholder="First Name" name="external_email_first_names[]" value="" type="text" />' +
                        '<input placeholder="Last Name" name="external_email_last_names[]" value="" type="text" />' +
                    '</p>' +
                    '<p>' +
                        '<input style="width: 350px;" placeholder="Email" name="external_email_addresses[]" value="" type="text" />' +
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

        $('#modal-title').html('Edit DNS List Email Report Template');

        return false;
    	
    });
	
});

</script>