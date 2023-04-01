<div id="trackstreet-dashboard">
    
    <!--  
    
    <?php //echo $retailer_query; ?>
    
    From: <?php echo $from; ?> 
    To: <?php echo $to; ?> 
    
    -->

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

    <!--  
    <div class="row">
        <div class="col-xs-12">
            <section id="repricingOverview" class="clear">
                <?php //echo $this->load->view('components/overview', '', TRUE); ?>
            </section>
        </div>    
    </div>
    -->
    
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="mini-stat clearfix bg-total-merchants rounded">
                <span class="mini-stat-icon"><i class="fa fa-building-o fg-total-merchants"></i></span>
                <div class="mini-stat-info">
                    <span><?php echo $number_of_merchants; ?></span>
                    Total Merchants
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="mini-stat clearfix bg-products-monitored rounded">
                <span class="mini-stat-icon"><i class="fa fa-tags fg-products-monitored"></i></span>
                <div class="mini-stat-info">
                    <span><?php echo $products_monitored; ?></span>
                    Products Monitored
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="mini-stat clearfix bg-market-violations rounded">
                <!-- 
                
                <?php //var_dump($marketplaces); ?>
                
                Marketplace Count: <?php echo count($marketplaces); ?>
                
                 -->
            
                <span class="mini-stat-icon"><i class="fa fa-shopping-cart fg-market-violations"></i></span>
                <div class="mini-stat-info">
                    <span style="font-size: 12px; margin-top: -4px;">Marketplace Violations</span>
                    <?php
                    
                    $i = 1;
                    $second_column_opened = FALSE;
                    $marketplace_count = 0;
                    
                    // hack because sometimes code is putting in marketplaces with 0 violations - Christophe
                    foreach ($marketplaces as $data)
                    {
                        $marketIndex = strtolower($data['marketplace']);
                        
                        if (!empty($violatedMarketplaces[$marketIndex]))
                        {
                            if (intval($market_violations[$marketIndex]) > 0)
                            {
                                $marketplace_count++;
                            }
                        }
                    }
                    
                    foreach ($marketplaces as $data):
                    
                        $marketIndex = strtolower($data['marketplace']);
                    
                        if (!empty($violatedMarketplaces[$marketIndex])):
                            if (intval($market_violations[$marketIndex]) > 0):
                                ?>
                                <?php 
                                
                                if ($i == 1)
                                {
                                	echo '<div style="display:inline; float: left;  margin-right: 10px;">';
                                }
                                
                                if ($i == 3)
                                {
                                	$second_column_opened = TRUE;
                                	echo '<div style="display:inline; float: left;">';
                                }                            
                                
                                ?>
                                <?php if ($i <= 3): ?>
                                    <p class="dashboard-marketplace-list-item">
                                        <?php echo $data['display_name'] ?>: <?php echo (isset($market_violations[$marketIndex])) ? $market_violations[$marketIndex] : 0; ?>
                                    </p>
                                <?php elseif ($i == 4): ?>
                                    <?php if ($marketplace_count > 4): ?>
                                        <p class="dashboard-marketplace-list-item">
                                            &amp; More
                                        </p>
                                    <?php else: ?>
                                        <p class="dashboard-marketplace-list-item">
                                            <?php echo $data['display_name'] ?>: <?php echo (isset($market_violations[$marketIndex])) ? $market_violations[$marketIndex] : 0; ?>
                                        </p>                                    
                                    <?php endif; ?>    
                                <?php endif; ?>    
                                <?php
                                
                                if (($i == 1 && $marketplace_count == 1) || ($i == 2 && $marketplace_count >= 2))
                                {
                                    echo '</div>';
                                }
                                
                                $i++;
                            endif;        
                        endif;
                        
                    endforeach;
                    ?>
                    <?php 

                    if ($second_column_opened)
                    {
                    	echo '</div>';
                    }
                    
                    ?>
                    <!-- marketplace count: <?php echo $marketplace_count; ?> -->
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="mini-stat clearfix bg-retailer-violations rounded">
                <span class="mini-stat-icon"><i class="fa fa-university fg-retailer-violations"></i></span>
                <div class="mini-stat-info">
                    <span><?php echo count($violatedRetailers); ?></span>
                    Retailers with Violations
                </div>
            </div>
        </div>
    </div>        

    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-widget" id="average-products-sold-chart-area">
                <div class="loading-box">
                    <img src="/images/gears.gif" /> Loading
                </div>    
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-widget" id="products-tracked-chart-area">
                <div class="loading-box">
                    <img src="/images/gears.gif" /> Loading
                </div>    
            </div>             
        </div>        
    </div>    
    
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-widget" id="merchants-tracked-chart-area">
                <div class="loading-box">
                    <img src="/images/gears.gif" /> Loading
                </div>    
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-widget" id="flot-notifications-chart-area">
                <div class="loading-box">
                    <img src="/images/gears.gif" /> Loading
                </div>    
            </div>             
        </div>        
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-widget" id="flot-product-violation-chart-area">
                <div class="loading-box">
                    <img src="/images/gears.gif" /> Loading
                </div>    
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-widget" id="average-violations-chart-area">
                <div class="loading-box">
                    <img src="/images/gears.gif" /> Loading
                </div>    
            </div>             
        </div>        
    </div>    
    
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-widget" id="most-violated-products-area">
                <div class="loading-box">
                    <img src="/images/gears.gif" /> Loading
                </div>    
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="dashboard-widget" id="most-volatile-products-area">
                <div class="loading-box">
                    <img src="/images/gears.gif" /> Loading
                </div>    
            </div>            
        </div>        
    </div>   
    
    <div class="row"> 
        <div class="col-xs-12">  
            <div class="dashboard-widget">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="<?php echo base_url() . 'whois/index' ?>">Who's Selling My Products Today</a>
                    </div>
                    <div class="panel-body">  
                        <?php echo $this->load->view('components/my_products', '', TRUE) ?>
                    </div>    
                </div>    
            </div>    
        </div>    
    </div>
    
</div>
 

<script type="text/javascript">

$(document).ready(function() {

    $.ajax({
        url: '/overview/product_violation_chart/flot',
        type: 'POST',
        dataType: 'html',
        data: '', 
        success : function(data) 
        {
            $('#flot-product-violation-chart-area').html(data);
        }
    });    

    $.ajax({
        url: '/overview/notifications_chart/flot',
        type: 'POST',
        dataType: 'html',
        data: '', 
        success : function(data) 
        {
            $('#flot-notifications-chart-area').html(data);
        }
    }); 

    $.ajax({
        url: '/overview/merchants_tracked_chart',
        type: 'POST',
        dataType: 'html',
        data: '', 
        success : function(data) 
        {
            $('#merchants-tracked-chart-area').html(data);
        }
    });
    
    $.ajax({
        url: '/overview/average_products_sold',
        type: 'POST',
        dataType: 'html',
        data: '', 
        success : function(data) 
        {
            $('#average-products-sold-chart-area').html(data);
        }
    });

    $.ajax({
        url: '/overview/daily_products_tracked',
        type: 'POST',
        dataType: 'html',
        data: '', 
        success : function(data) 
        {
            $('#products-tracked-chart-area').html(data);
        }
    });    

    $.ajax({
        url: '/overview/average_violations_chart',
        type: 'POST',
        dataType: 'html',
        data: '', 
        success : function(data) 
        {
            $('#average-violations-chart-area').html(data);
        }
    });
    
    // most violated products
    $.ajax({
        url: '/overview/most_violated_products',
        type: 'POST',
        dataType: 'html',
        data: '', 
        success : function(data) 
        {
            $('#most-violated-products-area').html(data);
        }
    });  

    // most volatile products
    $.ajax({
        url: '/overview/most_volatile_products',
        type: 'POST',
        dataType: 'html',
        data: '', 
        success : function(data) 
        {
            $('#most-volatile-products-area').html(data);
        }
    }); 

    $('.bg-total-merchants').hover(function() {
        $(this).toggleClass("bg-total-merchants-hover")       
    });

    $('.bg-total-merchants').click(function() {
        window.location.href = '/whois';
    });

    $('.bg-products-monitored').hover(function() {
        $(this).toggleClass("bg-products-monitored-hover")       
    });

    $('.bg-products-monitored').click(function() {
        window.location.href = '/catalog';
    });

    $('.bg-market-violations').hover(function() {
        $(this).toggleClass("bg-market-violations-hover")       
    });

    $('.bg-market-violations').click(function() {
        window.location.href = '/violationoverview/violations_by_marketplace';
    });

    $('.bg-retailer-violations').hover(function() {
        $(this).toggleClass("bg-retailer-violations-hover")       
    });    

    $('.bg-retailer-violations').click(function() {
        window.location.href = '/violationoverview/violations_by_retailers';
    });
        
});    

</script>

<?php if ($this->config->item('environment') == 'production'): ?>
    <!-- <script id="interact_55ce7b39bf703" data-unique="55ce7b39bf703" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7b39bf703"></script> -->
<?php endif; ?>

<?php if ($this->config->item('environment') == 'testbranch'): ?>
    <!--  
    <script id="interact_55c3e0144b734" data-unique="55c3e0144b734" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55c3e0144b734"></script>
    -->
<?php endif; ?>