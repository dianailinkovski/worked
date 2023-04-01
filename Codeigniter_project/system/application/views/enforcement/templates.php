<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Email Templates</strong>
        </h3>       
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview/violation_dashboard">MAP Enforcement</a> <i class="fa fa-angle-right"></i> <a href="/enforcement/templates">Email Templates</a>
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
    
        <?php if (isset($email_templates)): ?>    
            <table class="table table-bordered table-striped table-success table-responsive" id="enforcement-emails-table">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>Name</th>
                        <th>Subject</th>
                        <th>Notification Delay</th>
                        <th>Days To Repeat</th>
                        <th>Action</th>
                    </tr>
                </thead>    
                <tbody>    
                    <?php foreach ($email_templates->result() as $e_template): ?>
                        <tr>
                            <td>
                                <?php echo $e_template->notification_level; ?>
                            </td>
                            <td>
                                <?php echo convert_number_to_name($e_template->notification_level); ?>
                                Warning E-mail
                            </td>
                            <td>
                                <?php echo $e_template->subject; ?>
                            </td>
                            <td>
                                <?php echo $e_template->notify_after_days; ?>
                            </td>
                            <td>
                                <?php echo $e_template->no_of_days_to_repeat; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lg fa-fw fa-gear"></i> <span class="caret"></span></button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="/enforcement/template_settings/<?php echo $e_template->id; ?>">Edit Settings</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo site_url('enforcement/template/'.$e_template->id); ?>">Edit Message</a>  
                                        </li>
                                    </ul>                           
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>    
            </table>
        <?php else: ?>
            <p>
                No enforcement email templates found.
            </p>
        <?php endif; ?>
    </div>
</div> 

<?php if ($this->config->item('environment') == 'production'): ?>
    <!--  
    <script id="interact_55ce7fe345617" data-unique="55ce7fe345617" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7fe345617"></script>
    -->
    <script id="interact_55db664540eb9" data-unique="55db664540eb9" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55db664540eb9"></script>
<?php endif; ?>    
    