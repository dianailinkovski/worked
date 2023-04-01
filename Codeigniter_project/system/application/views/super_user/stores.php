<?php $this->load->view('global/super_user/header'); ?>

<div id="title-area" class="page-header">
    <h1>TrackStreet Stores</h1>
</div>

<table class="table table-bordered table-striped" id="stores-table">
    <thead>
        <tr>
            <th>
                Name
            </th>
            <th>
                Members
            </th>
            <th>
                Created
            </th>
            <th>
                Actions
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stores as $store): ?>
            <tr>
                <td>
                    <?php echo $store['store_name']; ?>
                </td>
                <td>
                    <?php echo $store['member_count']; ?>
                </td>
                <td>
                    <?php echo $store['created']; ?>
                </td>
                <td>
                    <a href="/super_user/edit_store/<?php echo $store['id']; ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>

<script type="text/javascript">

$('#stores-table').DataTable();

</script>

<?php $this->load->view('global/super_user/footer'); ?>