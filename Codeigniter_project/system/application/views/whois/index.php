<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Marketplaces Selling My Products</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> Market Visibility 
            <i class="fa fa-angle-right"></i> Who's Selling Now 
            <i class="fa fa-angle-right"></i> Marketplaces
        </div>
					
        <section id="repChartContainer"></section>
        
				<section id="keys_left" class="markets">
				
    				<?php if (count($marketplace_keys) > 0): ?>
    				
    				    <?php $marketCount = count($marketplace_keys); ?>
    						
    						<?php foreach($marketplace_keys as $key => $val): ?>
                    <span class="chkbox" onclick="hideShowSeries(this, '<?=$marketCount;?>')">
                    	<input type="checkbox" name="series[]" checked="checked" id="series_check_box_<?=$key?>" value="<?=$key?>" class="hidden" />
                    	<span class="squareKey" style="background-color: #<?=marketplace_graph_color($val)?>"></span><?php /*
                    	<img src="<?=frontImageUrl().marketplace_graph_color($val)?>_image.png" /> */?>
                    	<?=marketplace_display_name($val)?>
                    </span>
    						<?php endforeach; ?>
    						
            <?php endif; ?>
					
				</section>
				
				<section id="sellingMarketPlaces">
				    <?=$this->load->view('components/sellingMarketPlaces', '', TRUE)?>
				</section>

    </div>
</div>   

<?php echo $this->load->view('whois/components/interact_widget_script', '', TRUE); ?>    
    