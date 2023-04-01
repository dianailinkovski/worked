<div class="panel panel-default">

    <div class="panel-heading panel-breadcrumb-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Email Settings</strong>
        </h3>
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> MAP Enforcement <i class="fa fa-angle-right"></i> Email Settings
        </div>
        <div class="clear"></div>
    </div>
    
    <div class="panel-body">
    
						<section id="settings">
							<!-- <form action="<?=site_url('enforcement/settings')?>" method="post" id="enforcement_form" class="clear"> -->
								
								<input type="hidden" name="email_from" id="email_from" value="<?=$this->session->userdata('user_email');?>" />

								<?php if (!empty($message)) echo '<div class="message error">'.$message.'</div>';?>
                                
                                <div id="email_settings_grid"></div>
                                <?php /*
                                <div>
                                    <table>
                                        <tr>
                                            <th align="center" width="10%">No.</th>
                                            <th align="center" width="20%">Name</th>
                                            <th align="center" width="30%">Subject</th>
                                            <th align="center" width="10%">Notification Delay</th>
                                            <th align="center" width="10%">Days To Repeat</th>
                                            <th align="center" width="20%">Action</th>
                                        </tr>
                                        <?php if(isset($email_templates)):
                                                foreach($email_templates->result() as $e_template){
                                                    echo '<tr>';
                                                    echo '<td align="center">';
                                                    echo $e_template->notification_level;
                                                    echo '</td>';
                                                    echo '<td>';
                                                    echo convert_number_to_name($e_template->notification_level);
                                                    echo ' Warning E-mail</td>';
                                                    echo '<td>';
                                                    echo excerpt($e_template->subject);
                                                    echo '</td>';
                                                    echo '<td align="center">';
                                                    echo $e_template->notify_after_days;
                                                    echo '</td>';
                                                    echo '<td align="center">';
                                                    echo $e_template->no_of_days_to_repeat;
                                                    echo '</td>';
                                                    echo '<td align="center"><a href="';
                                                    echo site_url('enforcement/edit/'.$e_template->id);
                                                    echo '"><button type="button">Edit</button></a></td>';
                                                    echo '</tr>';
                                                }
                                        
                                         else: ?>
                                            <tr><td colspan="6">No records were found.</td></tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
								*/?>
							<!-- </form> -->
						</section>

    </div>						
</div>