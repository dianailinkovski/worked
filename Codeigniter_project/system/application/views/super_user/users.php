<?php $this->load->view('global/super_user/header'); ?>

<div id="title-area" class="page-header">
    <h1>TrackStreet Users</h1>
</div>

<table class="table table-bordered table-striped" id="users-table">
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
                Organization
            </th>
            <th>
                Actions
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td>
                    <?php echo $user['first_name']; ?>
                </td>
                <td>
                    <?php echo $user['last_name']; ?>
                </td>
                <td>
                    <?php echo $user['email']; ?>
                </td>
                <td>
                    <?php echo $user['organization']; ?>
                </td>
                <td>
                    <a href="/super_user/login_as/<?php echo $user['id']; ?>">Log In</a>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

<script type="text/javascript">

$('#users-table').DataTable();

$(document).ready(function() {
    
});

</script>

<?php $this->load->view('global/super_user/footer'); ?>