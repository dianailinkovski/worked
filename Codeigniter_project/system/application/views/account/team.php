<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Team Members</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> Account <i class="fa fa-angle-right"></i> <a href="/account/team">Team Members</a>
        </div>        

        <!-- s id: <?php echo $store_id; ?> -->
        
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
        
        <a id="add-team-member-button" href="/account/team_add">Add Team Member</a>
        
        <div id="team-members">
        
            <?php if (empty($members)): ?>
                <p>
                    You currently do not have any other team members added to your organization.
                </p>
            <?php else: ?>
                <table class="table table-bordered table-striped table-success table-responsive" id="team-table">
                    <thead>
                        <tr>
                            <th>
                                First Name
                            </th>
                            <th>
                                Last Name
                            </th>
                            <th>
                                Email
                            </th>
                            <th>
                                Role
                            </th>
                            <th>
                                Created
                            </th>
                            <th>
                                Status
                            </th>
                            <?php if ($this->role_id == 0 || $this->role_id == 2): ?>
                                <th>
                                    Actions
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td>
                                    <?php echo $member['first_name']; ?>
                                </td>
                                <td>
                                    <?php echo $member['last_name']; ?>
                                </td>
                                <td>
                                    <?php echo $member['email']; ?>
                                </td>
                                <td>
                                    <?php echo $member['role']; ?>
                                </td>
                                <td>
                                    <?php echo $member['created']; ?>
                                </td>
                                <td>
                                    <?php echo $member['status']; ?>
                                </td>
                                <?php if ($this->role_id == 0 || $this->role_id == 2): ?>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lg fa-fw fa-gear"></i> <span class="caret"></span></button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li>
                                                    <a href="/account/team_edit/<?php echo $member['uuid'] ?>">Edit Account</a>
                                                </li>
                                                <li>
                                                    <?php if ($member['status'] == 'Active'): ?>
                                                        <a href="/account/team_disable/<?php echo $member['uuid'] ?>" onclick="if (!confirm('Are you sure you would like to disable the member account for <?php echo $member['first_name']; ?> <?php echo $member['last_name']; ?>?')) return false;">Disable Account</a>
                                                    <?php else: ?>
                                                        <a href="/account/team_active/<?php echo $member['uuid'] ?>" onclick="if (!confirm('Are you sure you would like to re-activate the member account for <?php echo $member['first_name']; ?> <?php echo $member['last_name']; ?>?')) return false;">Re-activate Account</a>
                                                    <?php endif; ?>   
                                                </li>
                                                <li>
                                                    <a href="/account/team_resend_invite/<?php echo $member['uuid'] ?>">Resend Account<br/>Email</a>
                                                </li>
                                            </ul>                           
                                        </div>                          
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>        
        </div>

    </div>        
</div>

<script type="text/javascript">
      
$(document).ready(function() {

    $('#team-table').DataTable({
        "stateSave": true,
        "order": [[ 0, "asc" ]],
        // i = number of results info 
        // f = search  
        "dom": '<"top"f<"clear">>rt<"bottom"<"clear">>',
        "columnDefs": [ { "orderable": false, "targets": 6 } ]
    });

});

</script>

<?php if ($this->config->item('environment') == 'production'): ?>
    <!--  
    <script id="interact_55ce80d0820e2" data-unique="55ce80d0820e2" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce80d0820e2"></script>
    -->
    <script src="//interact.juststicky.com/js/embed/client.js/2028/interact_5627d4ae74957" id="interact_5627d4ae74957" data-text="Discuss this with Sticky Interact" data-unique="5627d4ae74957"></script>
<?php endif; ?>    