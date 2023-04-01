<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Categorize Catalog</strong>
        </h3>
    </div>
    
    <div class="panel-body">

        <p>Assign the catalog <b><?=$STORE['store_name'];?></b> to the appropriate categories below, then click the Save button:</p>
        
        	<?=$this->load->view('category/_status_msg', '', TRUE);?>
        
        <br>
        
        <style> ul{padding-left:15px}; </style>
        
        <form method="post" name="storeForm" id="storeForm" action="<?= $BASE_URL;?>category/store/storeId=<?= @$STORE['id'];?>">
            <input type="hidden" name="storeId" value="<?= $STORE['id'];?>">
            <input type="hidden" name="do" value="save" />
            <table>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" value="Save" style="float:right;"></td>
                </tr>
                <tr style="background:silver">
                    <th><input type="checkbox" id="selecctall">Select All</th>
                    <th>Category</th>
                </tr>
                <tr style="background:#E6E6E6">
                    <td>&nbsp;</td>
                    <td><?= $LIST?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" value="Save"></td>
                </tr>
            </table>
        </form>
        
        <script type="text/javascript">
            var checkedValues = [<?= $CHECKED_VALUES;?>];
        </script>
        
    </div>        
</div>