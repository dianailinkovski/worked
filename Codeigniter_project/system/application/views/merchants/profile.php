<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Merchant Profile</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> Market Visibility
            <i class="fa fa-angle-right"></i> <a href="/merchants">Merchant Info</a>
            <i class="fa fa-angle-right"></i> Merchant Profile
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
        
        <h3 class="large-subtitle">
            <?php echo $merchant_profile_name; ?>
        </h3>
        
        <ul class="nav nav-tabs" style="margin-bottom: 20px;">
            <li class="active">
                <a href="/merchants/profile/<?php echo $merchant_id; ?>">Basics</a>
            </li>
            <li>
                <a href="/merchants/profile_products/<?php echo $merchant_id; ?>">Products</a>
            </li>
            <li>
                <a href="/merchants/profile_violations/<?php echo $merchant_id; ?>">Violations</a>
            </li>
        </ul>        
        
        <div id="merchant-details">
                
            <h3 class="mp-subtitle">
                Overview
            </h3>
            
            <!--  
            <div class="row">
                <div class="col-md-8">.col-md-8</div>
                <div class="col-md-4">.col-md-4</div>
            </div>
            -->
            
            <div class="row">
                <div class="col-md-6">
                
                    <!-- using https://www.embed-map.com -->
                    <?php if ($merchant['google_map_query'] != ''): ?>
                        <div id="mp-google-map">
                            <div id="gmap_canvas" style="height:100%; width:100%;max-width:100%;">
                                <iframe style="height:100%;width:100%;border:0;" frameborder="0" src="https://www.google.com/maps/embed/v1/place?q=<?php echo $merchant['google_map_query']; ?>+United+States&key=<?php echo $google_maps_key; ?>"></iframe>
                            </div>
                        </div>
                    <?php endif; ?>    
                
                    <div class="merchant-detail-lists">
                    
                        <dl class="dl-horizontal">
                            <dt>Website:</dt>
                            <dd>
                                <?php if ($merchant['original_name'] == $merchant['marketplace']): ?>
                                    <a href="<?php echo $merchant['merchant_url']; ?>" target="_blank"><?php echo $merchant['merchant_url']; ?></a>
                                <?php else: ?>
                                    <a href="<?php echo $merchant['marketplace_url']; ?>" target="_blank"><?php echo ucfirst($merchant['marketplace']); ?> Seller Page</a>
                                <?php endif; ?>
                            </dd>                
                            <?php if ($merchant['original_name'] != $merchant['marketplace']): ?>
                                <dt>Marketplace:</dt>
                                <dd><?php echo ucfirst($merchant['marketplace']); ?></dd>                    
                            <?php endif; ?>
                            <?php if ($merchant['phone'] != ''): ?>
                                <dt>Phone:</dt>
                                <dd><?php echo $merchant['phone']; ?></dd>                            
                            <?php endif; ?>
                            <?php if ($merchant['fax'] != ''): ?>
                                <dt>Fax:</dt>
                                <dd><?php echo $merchant['fax']; ?></dd>                            
                            <?php endif; ?>        
                            <?php if ($merchant['contact_email'] != ''): ?>
                                <dt>Email:</dt>
                                <dd><?php echo $merchant['contact_email']; ?></dd>                            
                            <?php endif; ?>                                                
                            <dt>Tracking Started:</dt>
                            <dd><?php echo $first_tracking_date; ?></dd>
                            <dt>Last Violation:</dt>
                            <dd><?php echo $last_violation_date; ?></dd>
                        </dl>
                        
                        <div class="clear"></div>
                        
                        <!--  
                        <dl class="dl-horizontal">
                            <dt>Tracking Started:</dt>
                            <dd></dd>
                            <dt>Last Violation:</dt>
                            <dd></dd>
                        </dl>
                        -->
                        
                        <?php if ($this->role_id == 2): ?>
                            <p style="margin-top: 20px;">
                                <button data-toggle="modal" data-target="#dynamic-modal" id="edit-merchant-link" class="btn btn-success" data-modal-url="/merchants/edit/<?php echo $merchant_id; ?>">Edit Merchant Details</button>
                            </p>
                        <?php endif; ?>    
                        
                        <div class="clear"></div>
                                   
                    </div> 
                </div>
                <div class="col-md-6">
                    <div id="mp-staff-notes">
                    
                        <h3>
                            Internal Account Notes
                        </h3>
                    
                        <?php if (empty($staff_notes)): ?>
                            <p>
                                No notes have been added yet.
                            </p>
                        <?php else: ?>                    
                            <?php foreach ($staff_notes as $note): ?>
                                <div class="mp-staff-note">
                                    <p class="mp-staff-note-added-by">
                                        Note added by <b><?php echo $note['user']['first_name']; ?> <?php echo $note['user']['last_name']; ?></b> on <?php echo date('m/d/Y', strtotime($note['date'])); ?>:
                                    </p>
                                    <p class="mp-staff-note-message">
                                        <?php echo nl2br($note['entry']); ?>
                                    </p>
                                </div>
                                <hr />
                            <?php endforeach; ?>
                        <?php endif; ?>  
                        
                        <p>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-note-modal-form">Add New Internal Note</button>
                        </p>  
                        
                            <div class="sent-notice-model modal fade" id="add-note-modal-form" tabindex="-1" role="dialog">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">
                                        Add Internal Note                                       
                                    </h4>
                                  </div>
                                  
                                  <div id="save-merchant-note-form" style="padding: 30px;">
                                      <form action="/merchants/save_note/<?php echo $merchant_id; ?>" method="post">
                                          <p>
                                              Add a new internal note on this merchant that only your staff can see below.
                                          </p>
                                          <div>
                                              <textarea style="width: 80%; height: 200px;" name="note"></textarea>
                                          </div>
                                          <p style="margin-top: 20px;">
                                              <input value="Save Note" name="submit-button" type="submit" class="btn btn-success" />
                                          </p>
                                      </form>   
                                  </div>    
                                  
                                </div>
                              </div>
                            </div>                        
                        
                    
                    </div>
                </div>
            </div>        
                 
        </div>

        <div id="mp-product-count-chart-area">
            <h3 class="mp-subtitle">
                Products Listed
            </h3>
            <div id="product-count-chart" <?php if (empty($product_data_rows)): ?>style="display:none;"<?php endif; ?>>
                
            </div>
            <?php if (empty($product_data_rows)): ?>
                <p>
                    No products have been found for this merchant.
                </p>
            <?php endif; ?>
        </div>
               
        <div id="mp-linked-to">
            <h3 class="mp-subtitle">
                Also Linked To
            </h3>
            <div id="mp-linked-to-merchants-list">
                <?php if (!empty($other_merchants)): ?>
                    <ul class="list-group">
                        <?php foreach ($other_merchants as $other_merchant): ?>
                            <li class="list-group-item">
                                <a href="/merchants/profile/<?php echo $other_merchant['id']; ?>">                                 
                                    <?php if ($other_merchant['seller_id'] != $other_merchant['marketplace']): ?>
                                        <?php echo $other_merchant['original_name']; ?> (<?php echo ucfirst($other_merchant['marketplace']); ?> seller)
                                    <?php else: ?>
                                        <?php echo $other_merchant['original_name']; ?> (<?php echo $other_merchant['marketplace']; ?>.com)
                                    <?php endif; ?>
                                </a>
                                -
                                <a class="link-removal-button" data-modal-url="/merchants/link_removal_request/<?php echo $merchant_id; ?>/<?php echo $other_merchant['id']; ?>" href="/merchants/link_removal_request/<?php echo $merchant_id; ?>/<?php echo $other_merchant['id']; ?>" data-toggle="tooltip" data-placement="top" title="Request removal of merchant association link"><i class="fa fa-trash"></i></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>    
                    <p>
                        This merchant does not have any other merchant profiles link to it at the moment.
                    </p>    
                <?php endif; ?>   
                <?php if ($this->role_id == 2): ?> 
                    <p>
                        <button data-toggle="modal" data-target="#dynamic-modal" id="add-merchant-link" class="btn btn-success" data-modal-url="/merchants/add_merchant_link/<?php echo $merchant_id; ?>">Add a New Association</button>
                    </p>
                <?php endif; ?>    
            </div>
        </div> 
        
        <?php if (!empty($other_marketplace_merchants)): ?>
            <div id="mp-other-marketplace-profiles">
                <h3 class="mp-subtitle">
                    Other <?php echo ucfirst($merchant_marketplace); ?> Marketplace Identities Recorded
                </h3>
                <div id="mp-other-marketplace-profiles-list">
                    <ul class="list-group">
                        <?php foreach ($other_marketplace_merchants as $other_merchant): ?>
                            <li class="list-group-item">                             
                                <?php echo $other_merchant['original_name']; ?>
                                - Recorded on 
                                <?php if ($other_merchant['created_at'] != '0000-00-00 00:00:00'): ?>
                                    <?php echo date('m/d/Y', strtotime($other_merchant['created_at'])); ?>
                                <?php else: ?> 
                                    <?php echo date('m/d/Y', strtotime($other_merchant['created'])); ?>   
                                <?php endif; ?>    
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>        
        <?php endif; ?>       
        
        <div id="enforcement-protocol-area">
            <h3 class="mp-subtitle">
                Enforcement Protocol
            </h3>
            <form action="/merchants/send_to_settings/<?php echo $merchant_id; ?>" method="post">
                <p>
                    Violation Notifications for Merchant: <?php echo $notifications_onoff_dropdown; ?>
                </p>
                <p>
                    Send violation emails to:
                    <?php echo form_checkbox('primary_contact', 'true', $primary_contact_checkbox_value); ?> Primary Contact(s)
                    -- <?php echo form_checkbox('account_rep', 'true', $account_rep_checkbox_value); ?> Account Rep(s)
                    -- <?php echo form_checkbox('cc_address', 'true', $cc_address_checkbox_value); ?> CC Address(es)
                </p>
                <p>
                    Notification Template to Use: <?php echo $notification_template_dropdown; ?>
                </p>
                <!-- violation level: <?php echo $current_level; ?> -->
                <!--  
                <p> 
                    Current violation notification level:
                    <?php echo $violation_level_dropdown; ?> 
                </p>  
                -->    
                <?php if ($this->role_id == 2): ?>
                    <p>
                        <input type="submit" value="Save Protocol Setting" class="btn btn-success" />
                    </p>
                <?php endif; ?>  
            </form>
        </div>
        
        <div id="enforcement-contact-area">
            <h3 class="mp-subtitle">
                Enforcement Contact Information
            </h3>
            <div id="contacts">
                <div class="contact-list-section" id="primary-contacts">
                    <h4>
                        Primary Contact(s)
                    </h4>
                    <ul class="contact-list">
                        <?php foreach ($primary_contacts as $contact): ?>
                            <li class="contact-list-item">
                                <div class="contact-actions">
                                    <a class="edit-contact-launch" data-modal-url="/merchants/edit_contact/<?php echo $merchant_id; ?>/1/<?php echo $contact['uuid']; ?>" data-toggle="tooltip" data-placement="top" title="Edit Contact" href="#"><i class="fa fa-pencil-square-o"></i></a><br />
                                    <a onclick="if (!confirm('Are you sure you would like to delete this contact?')) return false;" href="/merchants/delete_contact/<?php echo $merchant_id; ?>/<?php echo $contact['uuid']; ?>" data-toggle="tooltip" data-placement="top" title="Delete Contact"><i class="fa fa-trash"></i></a>                                   
                                </div>
                                <div class="contact-details">
                                    <h5>
                                        <?php echo $contact['first_name']?> <?php echo $contact['last_name']?>
                                    </h5>
                                    <p>
                                        <?php echo $contact['email']; ?>
                                    </p>
                                    <?php if ($contact['phone'] != ''): ?> 
                                        <p>
                                            <?php echo $contact['phone']; ?>
                                        </p> 
                                    <?php endif; ?>  
                                </div>                    
                            </li>
                        <?php endforeach; ?>
                        <?php if ($this->role_id == 2): ?> 
                            <li class="add-contact-button">
                                <a class="btn btn-success add-contact-launch" id="add-primary-contact-button" data-modal-url="/merchants/edit_contact/<?php echo $merchant_id; ?>/1" href="#">+</a>
                            </li>    
                        <?php endif; ?>        
                    </ul>
                    <div class="clear"></div> 
                </div>
                <div class="contact-list-section" id="account-rep-contacs">
                    <h4>
                        Account Rep(s)
                    </h4>
                    <ul class="contact-list">
                        <?php foreach ($account_rep_contacts as $contact): ?>
                            <li class="contact-list-item">
                                <div class="contact-actions">
                                    <a class="edit-contact-launch" data-modal-url="/merchants/edit_contact/<?php echo $merchant_id; ?>/2/<?php echo $contact['uuid']; ?>" data-toggle="tooltip" data-placement="top" title="Edit Contact" href="#"><i class="fa fa-pencil-square-o"></i></a><br />
                                    <a onclick="if (!confirm('Are you sure you would like to delete this contact?')) return false;" href="/merchants/delete_contact/<?php echo $merchant_id; ?>/<?php echo $contact['uuid']; ?>" data-toggle="tooltip" data-placement="top" title="Delete Contact"><i class="fa fa-trash"></i></a>                                   
                                </div>
                                <div class="contact-details">                            
                                    <h5>
                                        <?php echo $contact['first_name']?> <?php echo $contact['last_name']?>
                                    </h5>
                                    <p>
                                        <?php echo $contact['email']; ?>
                                    </p>
                                    <?php if ($contact['phone'] != ''): ?> 
                                        <p>
                                            <?php echo $contact['phone']; ?>
                                        </p> 
                                    <?php endif; ?> 
                                </div>                     
                            </li>
                        <?php endforeach; ?> 
                        <?php if ($this->role_id == 2): ?>
                            <li class="add-contact-button">
                                <a class="btn btn-success add-contact-launch" id="add-account-rep-contact-button" data-modal-url="/merchants/edit_contact/<?php echo $merchant_id; ?>/2" href="#">+</a>
                            </li>    
                        <?php endif; ?>    
                    </ul>
                    <div class="clear"></div>                
                </div>
                <div class="contact-list-section" id="cc-address-contacts">
                    <h4>
                        CC Address(es)
                    </h4>
                    <ul class="contact-list">
                        <?php foreach ($cc_address_contacts as $contact): ?>
                            <li class="contact-list-item">
                                <div class="contact-actions">
                                    <a class="edit-contact-launch" data-modal-url="/merchants/edit_contact/<?php echo $merchant_id; ?>/2/<?php echo $contact['uuid']; ?>" data-toggle="tooltip" data-placement="top" title="Edit Contact" href="#"><i class="fa fa-pencil-square-o"></i></a><br />
                                    <a onclick="if (!confirm('Are you sure you would like to delete this contact?')) return false;" href="/merchants/delete_contact/<?php echo $merchant_id; ?>/<?php echo $contact['uuid']; ?>" data-toggle="tooltip" data-placement="top" title="Delete Contact"><i class="fa fa-trash"></i></a>                                   
                                </div>
                                <div class="contact-details">                            
                                    <h5>
                                        <?php echo $contact['first_name']?> <?php echo $contact['last_name']?>
                                    </h5>
                                    <p>
                                        <?php echo $contact['email']; ?>
                                    </p>
                                    <?php if ($contact['phone'] != ''): ?> 
                                        <p>
                                            <?php echo $contact['phone']; ?>
                                        </p> 
                                    <?php endif; ?>    
                                </div>                      
                            </li>
                        <?php endforeach; ?> 
                        <?php if ($this->role_id == 2): ?>
                            <li class="add-contact-button">
                                <a class="btn btn-success add-contact-launch" id="add-cc-address-contact-button" data-modal-url="/merchants/edit_contact/<?php echo $merchant_id; ?>/3" href="#">+</a>
                            </li>    
                        <?php endif; ?>        
                    </ul>
                    <div class="clear"></div>                 
                </div>                
            </div>
        </div>
            
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

function showTooltip(x, y, contents) {
    jQuery('<div id="tooltip" class="tooltipflot">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5
    }).appendTo("body").fadeIn(200);
}

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

    $('.link-removal-button').click(function(){

        $('#dynamic-modal').modal('show');

        var modal_iframe_src = $(this).attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Request Removal of Merchant Association');

        return false;
    	
    });

    $('#edit-merchant-link').click(function() {

        $('#dynamic-modal').modal('show');

        var modal_iframe_src = $(this).attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Edit Merchant Details');

        return false;
    	
    });    

    $('#add-merchant-link').click(function() {

        var modal_iframe_src = $('#add-merchant-link').attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Add Merchant Link');
    	
    });	

    $('.edit-contact-launch').click(function() {

        $('#dynamic-modal').modal('show');

        var modal_iframe_src = $(this).attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Edit Contact');

        return false;
    	
    });

    $('.add-contact-launch').click(function() {

        $('#dynamic-modal').modal('show');

        var modal_iframe_src = $(this).attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Add Contact');

        return false;
    	
    });
    
		$('[data-toggle="tooltip"]').tooltip();
	
    if ($('#product-count-chart').length) {
    	
        //var series1 = [[0, 10], [1, 6], [2,3], [3, 8], [4, 5], [5, 13], [6, 8]];
        
        
        var series1 = [
            <?php foreach ($product_data_rows as $data_row): ?>
                ["<?php echo date('M', strtotime($data_row['select_date'])); ?> <br/><?php echo date('j', strtotime($data_row['select_date'])); ?>", <?php echo $data_row['product_count']; ?>],
            <?php endforeach; ?>
        ];
        
    
        var plot = $.plot($("#product-count-chart"),
            [ { data: series1,
                //label: "Series 1",
                //color: "#8cc152"
                color: "#00a0d1"
            },
            ],
            {
                canvas: false,
                series: {
                    bars: {
                        show: true,
                        fill: true,
                        lineWidth: 1,
                        fillColor: {
                            colors: [ 
                                { opacity: 0.5 },
                                { opacity: 0.5 }
                            ]
                        }
                    },
                    points: {
                        show: false
                    },
                    shadowSize: 0
                },
                legend: {
                    position: 'nw'
                },
                grid: {
                    hoverable: true,
                    clickable: true,
                    borderColor: '#ddd',
                    borderWidth: 1,
                    labelMargin: 10,
                    backgroundColor: '#fff'
                },
                yaxis: {
                    //min: 0,
                    //max: 15,
                    color: '#eee',
                    tickDecimals: 0
                },
                xaxis: {
                	mode: "categories",
                    color: '#eee',
                    tickSize: 5
                }
            });
    
        var previousPoint = null;
        
        $("#product-count-chart").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));
    
            if(item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
    
                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];
    
                    showTooltip(item.pageX, item.pageY, y + ' products listed');
                }
    
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
    
        });
    
        $("#product-count-chart").bind("plotclick", function (event, pos, item) {
            if (item) {
                plot.highlight(item.series, item.datapoint);
            }
        });
    }
});
</script>

<?php echo $this->load->view('merchants/parts/interact_embed', '', TRUE); ?>