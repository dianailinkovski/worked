<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Sent Violation Notices</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> <i class="fa fa-angle-right"></i> Violation Reports <i class="fa fa-angle-right"></i> Sent Violation Notices
        </div>
        
        <div id="filters">
            <form action="/violationoverview/sent_notices" method="post">
                <?php echo $this->load->view('components/bydate', '', TRUE); ?>
                <p>
                    <input class="btn btn-success" type="submit" value="Filter by Dates" />
                </p>
            </form>
        </div>  
    
        <?php if (!empty($notices)): ?>
            <table class="table table-bordered table-striped table-success table-responsive exportable" id="sent-notices-table">
                <thead>
                    <tr>
                	      <th align="left">Date</th>  
                		    <th align="left">Merchant</th>
                		    <th align="left">Marketplace</th>
                		    <th align="left">Sent To</th>
                		    <th align="left">Title</th>
                		    <th align="left">Message</th>
                    </tr>
                </thead>
							  <tbody>
                <?php foreach ($notices as $notice): ?>
                    <tr>
                        <td>
                            <?php echo date('M j, Y', strtotime($notice['date'])); ?>
                        </td>
                        <td>
                            <?php if (isset($notice['merchant']['merchant_name'])): ?>
                                <a href="/merchants/profile/<?php echo $notice['merchant_id']; ?>"><?php echo $notice['merchant']['merchant_name']; ?></a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>    
                        </td>
                        <td>
                            <?php if ($notice['merchant']['original_name'] == $notice['merchant']['marketplace']): ?>
                                N/A
                            <?php else: ?>
                                <?php echo ucfirst($notice['merchant']['marketplace']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($notice['email_to']): ?>
                                <?php echo $notice['email_to']; ?>
                            <?php else: ?>
                                Sent to <?php if (isset($notice['merchant']['marketplace'])): ?><?php echo ucfirst($notice['merchant']['marketplace']); ?> <?php endif; ?>Marketplace Seller    
                            <?php endif; ?>    
                        </td>
                        <td>
                            <?php echo $notice['title']; ?>
                        </td>
                        <td>
                        
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#message-<?php echo $notice['id']; ?>">
                              View
                            </button>
                            
                            <div class="modal fade" id="message-<?php echo $notice['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog">
                                <div class="modal-content">
                            
                                  <div class="modal-header">
                                    <div class="hidden-xs">
                                      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                      <h4 class="modal-title" id="myModalLabel">
                                          <?php echo $notice['title']; ?>
                                          - Merchant: 
                                          <?php if (isset($notice['merchant']['merchant_name'])): ?>
                                            <?php echo $notice['merchant']['merchant_name']; ?>
                                          <?php else: ?>
                                            N/A
                                          <?php endif; ?>                                       
                                      </h4>
                                    </div>
                                    <div class="hidden-sm hidden-md hidden-lg sml-header">
                                      <button type="button" class="close sml-txt" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                      <h4 class="modal-title sml-txt" id="myModalLabel">
                                          <?php echo $notice['title']; ?>
                                          - Merchant: 
                                          <?php if (isset($notice['merchant']['merchant_name'])): ?>
                                            <?php echo $notice['merchant']['merchant_name']; ?>
                                          <?php else: ?>
                                            N/A
                                          <?php endif; ?> 
                                      </h4>
                                    </div>
                                  </div>
                                  
                                  <div class="modal-body">
                                      <?php if ($notice['full_message'] != ''): ?>
                                          <iframe style="width: 100%; height: 100%;" src="/violationoverview/sent_notice_message/<?php echo $notice['id']; ?>" width="100%" height="600"></iframe>
                                          <?php //echo $notice['full_message']; ?>
                                      <?php else: ?>
                                          <div class="alert alert-warning" role="alert">
                                              With older notifications, we could only log a portion of the full message that was sent to the violator. 
                                              A full message was sent even though a partial version is only shown here.
                                          </div>
                                          <div class="modal-body" style="overflow-y: scroll; height: 400px;">
                                              <?php echo nl2br($notice['regarding']); ?>
                                          </div>
                                      <?php endif; ?>   
                                  </div>
                                  
                                </div>
                              </div>
                            </div>
                                 
                        </td>
                    </tr>
								<?php endforeach; ?>
							</tbody>
						</table>
        <?php else: ?>
            <hr />
            <p>
                No notices have been sent yet in the specified time frame.
            </p>
        <?php endif; ?>
            
    </div>
</div>

<script type="text/javascript">
      
$(document).ready(function() {

	$('#date_from').datepicker({
		dateFormat: 'yy-mm-dd',
		minDate:'-20y',
		maxDate:'0d',
		onSelect:function(dateText, e){
			$('input[name=time_frame]:radio').each(function(){
				$(this).attr('checked',false);
			});
		},
		onClose: function(){
			if(!is_date_range_valid()){
				$(this).val('');
				return false;
			}
			else
			{
				//elaborate_keyword();
			}
		}
	});

	$('#date_to').datepicker({
		dateFormat: 'yy-mm-dd',
		minDate:'-20y',
		maxDate:'0d',
		onSelect:function(dateText, e){
			$('input[name=time_frame]:radio').each(function(){
				$(this).attr('checked',false);
			});
		},
		onClose: function(dateText, e){
			if(!is_date_range_valid()){
				$(this).val('');
				return false;
			}
			else
			{
				//elaborate_keyword();
			}
		}
	});
	
	$("#date_from_a").click(function(){$('#date_from').click();});
	$("#date_to_a").click(function(){$('#date_to').click();});	

	$( "input[name=time_frame]:radio" ).change(function() {
		var minusDays = $(this).val();
		if (minusDays==24){ minusDays = 1; }
		var today = $.datepicker.formatDate('yy-mm-dd', new Date());
		//var dateFrom = $.datepicker.setDate($.datepicker.getDate('yy-mm-dd')-7);
		$('#date_to').datepicker('setDate', today);
		$('#date_from').datepicker('setDate', '-'+minusDays+'d');
		//alert ('time frame: '+$(this).val()+' today: ' + today+' dateFrom: '+ '-'+minusDays+'d');
		//elaborate_keyword();
	});	

    var table = $('#sent-notices-table').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "order": [[ 0, "desc" ]],
        "stateSaveCallback": function (settings, data) {
            $.ajax({
                "url": "/ajax/table_state_save/sent-notices-table",
                "data": data,
                "dataType": "json",
                "type": "POST",
                "success": function () {}    
            });
        },
        "stateLoadCallback": function (settings) {
            var o;
        
            $.ajax({
              "url": "/ajax/table_state_load/sent-notices-table",
              "async": false,
              "dataType": "json",
              "success": function (json) {
                o = json;
              }
            });
         
            return o;
        },             
        // i = number of results info 
        // f = search  
        "dom": 'R<"top"<"bulk-actions">f<"clear">>rt<"bottom"ilp<"clear">>'
    });
	
});

</script>

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>
