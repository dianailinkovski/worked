<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Do Not Sell</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> MAP Enforcement
            <i class="fa fa-angle-right"></i> Do Not Sell List
        </div> 
        
        <hr />
        
        <div id="do-not-sell-area">
            
            <div class="table-top-section-header">
                <h2 class="table-top-section-header-subtitle">
                    Do Not Sell List
                </h2>
                <div class="table-top-section-actions">
                
                    <div id="dns-search-area">
                        Search: <input class="table-search-field" type="text" id="dns-search-field" />
                    </div>                
                    <div id="reports-top-area">
                        <div id="report-save-options">
                            <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                            <div class="clear"></div>            
                        </div>
                        <div class="clear"></div>
                    </div>                 

                    <div class="clear"></div>    
                </div>   
                <div class="clear"></div> 
            </div>
            
            <div id="dns-table-area">
            
                <?php if (!empty($dns_merchants)): ?>
                    <table class="table table-bordered table-striped table-success table-responsive reportTable exportable" id="dns-merchants-table">
                        <thead>
                            <tr>
                                <th>Merchant Name</th>
                                <th>Website</th>
                                <th>DNS Start</th>
                                <th>DNS Removal</th>
                                <th>Times on List</th>
                                <th>Status</th>
                                <?php if ($this->role_id != 2): ?>
                                
                                <?php else: ?> 
                                    <th>Actions</th> 
                                <?php endif; ?>                          
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dns_merchants as $merchant): ?>
                                <tr>
                                    <td>
                                        <a href="/merchants/profile/<?php echo $merchant['id']; ?>"><?php echo $merchant['profile_name']; ?></a>
                                    </td>
                                    <td>
                                        <?php if ($merchant['original_name'] == $merchant['marketplace'] || $merchant['seller_id'] == $merchant['marketplace']): ?>
                                            <a href="<?php echo $merchant['merchant_url']; ?>" target="_blank"><?php echo $merchant['merchant_url']; ?></a>
                                        <?php else: ?>
                                            <a href="<?php echo $merchant['marketplace_url']; ?>" target="_blank"><?php echo ucfirst($merchant['marketplace']); ?> Seller Page</a>
                                        <?php endif; ?>                        
                                    </td>
                                    <td>
                                        <?php echo $merchant['start_date']; ?>
                                    </td>
                                    <td>
                                        <?php echo $merchant['removal_date']; ?>
                                    </td>  
                                    <td>
                                        <?php echo $merchant['num_of_times']; ?>
                                    </td> 
                                    <td>
                                        <?php if (intval($merchant['is_permanent']) == 1): ?>
                                            Permanent
                                        <?php else: ?>
                                            Temporary
                                        <?php endif; ?>
                                    </td> 
                                    <?php if ($this->role_id != 2): ?> 
                                    
                                    <?php else: ?>            
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lg fa-fw fa-gear"></i> <span class="caret"></span></button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li>
                                                        <a class="edit-dns-entry-button" data-modal-url="/enforcement/do_not_sell_edit/<?php echo $merchant['id']; ?>" href="#">Edit</a>
                                                    </li>
                                                    <li>
                                                        <a class="remove-dns-entry-button"  data-modal-url="/enforcement/do_not_sell_remove/<?php echo $merchant['id']; ?>" href="">Remove</a>  
                                                    </li>
                                                </ul>                           
                                            </div>                            
                                        </td>  
                                    <?php endif; ?>                                                                                                                             
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table> 
                    
                    <script type="text/javascript">
                    
                    $(document).ready(function() {
                    
                        var dns_table = $('#dns-merchants-table').DataTable({
                            "stateSave": true,
                            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                            "stateSaveCallback": function (settings, data) {
                                $.ajax({
                                    "url": "/ajax/table_state_save/dns-merchants-table",
                                    "data": data,
                                    "dataType": "json",
                                    "type": "POST",
                                    "success": function () {}    
                                });
                            },
                            "stateLoadCallback": function (settings) {
                                var o;
                            
                                $.ajax({
                                  "url": "/ajax/table_state_load/dns-merchants-table",
                                  "async": false,
                                  "dataType": "json",
                                  "success": function (json) {
                                    o = json;
                                  }
                                });
                             
                                return o;
                            },
                            "dom": 'Rrt<"bottom"ilp<"clear">>'
                        });
            
                        // see http://www.datatables.net/extensions/fixedheader/options
                        new $.fn.dataTable.FixedHeader(dns_table, {
                            "offsetTop": 55
                        });	

                        $('#dns-search-field').keyup(function(){
                            dns_table.search($(this).val()).draw();
                        });
                    	
                    });
                    
                    </script> 
                    
                <?php else: ?>
                    <p id="do-not-sell-empty-list">
                        No merchants are classified in this group.
                    </p>
                <?php endif; ?>  
                 
            </div>
        </div>         
        
        <hr />

        <?php foreach ($level_merchants as $level => $merchants): ?>
        
            <div class="level-list-area">
        
                <div class="table-top-section-header">
                    <h2 class="table-top-section-header-subtitle">
                        Level <?php echo $level; ?> Violators
                    </h2>
                    <div class="table-top-section-actions">
                        Search: <input class="table-search-field" type="text" id="level-<?php echo $level; ?>-search-field" />
                    </div>   
                    <div class="clear"></div> 
                </div>
                
                <div class="level-list-table-area">
                
                    <?php if (!empty($merchants)): ?>
                        <table class="table table-bordered table-striped table-success table-responsive" id="merchants-level-<?php echo $level; ?>-table">
                            <thead>
                                <tr>
                                    <th>Merchant Name</th>
                                    <th>Website</th>
                                    <th>Last Violation</th>
                                    <?php if ($this->role_id != 2): ?>
                                    
                                    <?php else: ?>
                                        <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($merchants as $merchant): ?>
                                    <tr>
                                        <td>
                                            <a href="/merchants/profile/<?php echo $merchant['id']; ?>"><?php echo $merchant['profile_name']; ?></a>
                                        </td>
                                        <td>
                                            <?php if ($merchant['original_name'] == $merchant['marketplace'] || $merchant['seller_id'] == $merchant['marketplace']): ?>
                                                <a href="<?php echo $merchant['merchant_url']; ?>" target="_blank"><?php echo $merchant['merchant_url']; ?></a>
                                            <?php else: ?>
                                                <a href="<?php echo $merchant['marketplace_url']; ?>" target="_blank"><?php echo ucfirst($merchant['marketplace']); ?> Seller Page</a>
                                            <?php endif; ?>                        
                                        </td> 
                                        <td>
                                            <?php echo $merchant['last_violation_date']; ?>
                                        </td>  
                                        <?php if ($this->role_id != 2): ?>
                                        
                                        <?php else: ?>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lg fa-fw fa-gear"></i> <span class="caret"></span></button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        <li>
                                                            <a class="edit-list-entry-button" data-modal-url="/enforcement/edit_list_entry/<?php echo $merchant['id']; ?>" href="#">Edit</a>
                                                        </li>
                                                    </ul>                           
                                                </div>                                    
                                            </td>
                                        <?php endif; ?>                                                                                                                                    
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table> 
                        
                        <script type="text/javascript">
                        
                        $(document).ready(function() {
                        
                            var merchants_level_<?php echo $level; ?>_table = $('#merchants-level-<?php echo $level; ?>-table').DataTable({
                                "stateSave": true,
                                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                                "stateSaveCallback": function (settings, data) {
                                    $.ajax({
                                        "url": "/ajax/table_state_save/merchants-level-<?php echo $level; ?>-table",
                                        "data": data,
                                        "dataType": "json",
                                        "type": "POST",
                                        "success": function () {}    
                                    });
                                },
                                "stateLoadCallback": function (settings) {
                                    var o;
                                
                                    $.ajax({
                                      "url": "/ajax/table_state_load/merchants-level-<?php echo $level; ?>-table",
                                      "async": false,
                                      "dataType": "json",
                                      "success": function (json) {
                                        o = json;
                                      }
                                    });
                                 
                                    return o;
                                },
                                "dom": 'Rrt<"bottom"ilp<"clear">>'
                            });
                
                            new $.fn.dataTable.FixedHeader(merchants_level_<?php echo $level; ?>_table, {
                                "offsetTop": 55
                            });	
        
                            $('#level-<?php echo $level; ?>-search-field').keyup(function(){
                                merchants_level_<?php echo $level; ?>_table.search($(this).val()).draw();
                            });
                        	
                        });
                        
                        </script> 
                        
                    <?php else: ?>    
                        <p>
                            No merchants are classified in this group.
                        </p>
                    <?php endif; ?>
                    
                </div>        
            
            </div>    
            
            <hr />
            
        <?php endforeach; ?>  
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

var data_changed = false;
                
function set_data_change_to_true()
{
    // if data on page was changed via modal
    data_changed = true;
}
      
$(document).ready(function() {

    $('#dynamic-modal').on('hidden.bs.modal', function () {
        
        //alert(data_changed);
        
        if (data_changed)
        {
            window.location.reload(true);
        }
    });

    $('.edit-list-entry-button').click(function(){

        data_changed = false;

        $('#dynamic-modal').modal('show');

        var modal_iframe_src = $(this).attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Edit List Entry');

        return false;
    	
    });

    $('.edit-dns-entry-button').click(function(){

        data_changed = false;

        $('#dynamic-modal').modal('show');

        var modal_iframe_src = $(this).attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Edit Do Not Sell Entry');

        return false;
    	
    });   

    $('.remove-dns-entry-button').click(function(){

        data_changed = false;

        $('#dynamic-modal').modal('show');

        var modal_iframe_src = $(this).attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Remove Do Not Sell Entry');

        return false;
    	
    });      

});

</script>  

<?php if ($this->config->item('environment') == 'production'): ?>
    <script src="//interact.juststicky.com/js/embed/client.js/2028/interact_56294f677c3a3" id="interact_56294f677c3a3" data-text="Discuss this with Sticky Interact" data-unique="56294f677c3a3"></script>
<?php endif; ?>    
    