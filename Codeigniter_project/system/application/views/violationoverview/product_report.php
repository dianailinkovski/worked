<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Product Violation Summary</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> 
            <i class="fa fa-angle-right"></i> Violation Reports 
            <i class="fa fa-angle-right"></i> Product Violation Summary
        </div>
        
        <div id="filters">
            <form action="/violationoverview/product_report" method="post">
            
                <?php //echo $this->load->view('components/bydate', '', TRUE); ?>
                
                <!-- start bydate -->
                
                <?php
                if (isset($date_from))
                {
                    if (is_numeric($date_from))
                    {
                        $date_from = date('Y-m-d', $date_from);
                    }
                }
                if (isset($date_to))
                {
                    if (is_numeric($date_to))
                    {
                        $date_to = date('Y-m-d', $date_to);
                    }
                }
                ?>
                
                <section class="clear select_report<?= ($display ? '' : ' hidden') . ($is_first ? ' search_first' : ''); ?>" id="date_range">
                    <div class="leftCol">
                        <label>Date Range</label>
                    </div>
                    <div class="rightCol">
                        <div class="inputContainer">
                            <input class="start dateInput" value="<?= $time_frame ? 'Start' : $date_from ?>" id="date_from" name="date_from" max="<?= date('Y-m-d'); ?>"/>
                            <img src="<?= frontImageUrl() ?>icons/24/50.png" alt="Start Date" id="date_from_a" width="24" height="24" class="imgIcon" />
                        </div>
                        <div class="inputContainer">
                            <input class="start dateInput" value="<?= $time_frame ? 'Stop' : $date_to ?>" id="date_to" name="date_to" max="<?= date('Y-m-d'); ?>"/>
                            <img src="<?= frontImageUrl() ?>icons/24/50.png" alt="Stop Date" id="date_to_a" width="24" height="24" class="imgIcon" />
                        </div>
                    </div>
                </section>
                <!-- end bydate -->
                
                
                <p>
                    <input class="btn btn-success" type="submit" value="Filter by Dates" />
                </p>
            </form>
        </div>
        
        <div id="reports-top-area">
            <div id="report-save-options">
                <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                <div class="clear"></div>            
            </div>
            <div class="clear"></div>
        </div>         
        
        <div id="report-area">
            <table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="product-violation-summary">
                <thead>
                    <tr>
                	      <th align="left">Product</th>  
                		    <?php foreach ($month_index_array as $month_index): ?>
                		        <th align="left"><?php echo $month_index; ?></th> 
                		    <?php endforeach; ?>
                    </tr>
                </thead>
							  <tbody>
							      <?php foreach ($product_id_array as $product_id): ?>
							          <tr>
    							          <td>
    							              <?php echo $product_names[$product_id]; ?>
    							          </td>
    							          <?php foreach ($month_index_array as $month_index): ?>
    							              <td>
    							                  <?php if (isset($monthly_data[$month_index][$product_id])): ?>
    							                      <?php echo $monthly_data[$month_index][$product_id]; ?>
    							                  <?php else: ?>
    							                      N/A
    							                  <?php endif; ?>    
    							              </td>
    							          <?php endforeach; ?>							          
    							      </tr>        
							      <?php endforeach; ?>
                </tbody>
            </table>        
        </div> 
        
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

    var table = $('#product-violation-summary').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "stateSaveCallback": function (settings, data) {
            $.ajax({
                "url": "/ajax/table_state_save/product-violation-summary",
                "data": data,
                "dataType": "json",
                "type": "POST",
                "success": function () {}    
            });
        },
        "stateLoadCallback": function (settings) {
            var o;
        
            $.ajax({
              "url": "/ajax/table_state_load/product-violation-summary",
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

    // see http://www.datatables.net/extensions/fixedheader/options
    // http://datatables.net/extensions/fixedheader/
    // http://datatables.net/download/release
    new $.fn.dataTable.FixedHeader(table, {
        "offsetTop": 55
    });	

});

</script>	