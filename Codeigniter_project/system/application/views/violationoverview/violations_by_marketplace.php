<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Marketplace Violations</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> <i class="fa fa-angle-right"></i> Violation Reports <i class="fa fa-angle-right"></i> <a href="/violationoverview/violations_by_marketplace">Violations by Marketplace</a>
        </div>
        
        <div id="reports-top-area">
            <div id="report-save-options">
                <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                <div class="clear"></div>            
            </div>
            <div class="clear"></div>
        </div>         
    
        <?php if (!empty($violatedMarketplaces)): ?>
            <table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="violations-by-marketplace">
                <thead>
                    <tr class="row_title">
                        <th class="overviewtitleTh" width="40%">Marketplace</th>
                        <th class="overviewtitleTh" width="20%">Products</th>
                        <th class="overviewtitleTh" width="20%">Violations</th>
                        <th class="overviewtitleTh" width="20%">Last Tracking</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($marketplaces as $data):
                        $marketIndex = strtolower($data['marketplace']);
                        if (!empty($violatedMarketplaces[$marketIndex])):
                            $crawl_info = !empty($last_crawl[$marketIndex]) ? $last_crawl[$marketIndex] : FALSE;
                            $crawl_start = !empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : '';
                            ?>
                            <tr>
                                <td><a href="<?php echo base_url() . 'violationoverview/report_marketplace/' . $marketIndex; ?>"><?php echo $data['display_name'] ?></a></td>
                                <td><?php echo number_format($data['total_products']); ?></td>
                                <td><?php echo (isset($market_violations[$marketIndex])) ? $market_violations[$marketIndex] : 0; ?></td>
                                <td><?php echo $crawl_start ?></td>
                            </tr>
                        <?php
                        endif;
                    endforeach;
                    ?>
                </tbody>
            </table>
            
            <script type="text/javascript">
                  
            $(document).ready(function() {
            
                $('#violations-by-marketplace').DataTable({
                    "stateSave": true,
                    "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                    "stateSaveCallback": function (settings, data) {
                        $.ajax({
                            "url": "/ajax/table_state_save/violations-by-marketplace",
                            "data": data,
                            "dataType": "json",
                            "type": "POST",
                            "success": function () {}    
                        });
                    },
                    "stateLoadCallback": function (settings) {
                        var o;
                    
                        $.ajax({
                          "url": "/ajax/table_state_load/violations-by-marketplace",
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
            
        <?php else: ?>
            <p>
                No marketplace violations found.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>    