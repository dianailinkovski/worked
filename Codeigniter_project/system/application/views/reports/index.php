<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Pricing Over Time</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> Market Visibility <i class="fa fa-angle-right"></i> <a href="/reports">Pricing by Product</a>
        </div>
    
					<form method="post" name="reportfrm" id="reportfrm" action="<?=site_url('reports/show')?>">
						<input type="hidden" name="formSubmit" value="1">
						<?php
						foreach ($optArray as $comp => $opts):
							echo $this->load->view('components/' . $comp, $opts, TRUE);
						endforeach;
						?>
					</form>

					<?php if ( ! empty($Data)): ?>
						<?=$this->load->view('components/save_options', '', TRUE);?>
						<div class="clearAfter"></div>
						<?=$this->load->view('components/chart', array('gData', $gData), TRUE);?>
					<?php endif; ?>

					<?php if ($submitted):?>
					<div class="reportTables">
                    <?php if ( empty($Data) ): ?>
                        <div class="error">Sorry, no data exists for this query.</div>
                    <?php else : ?>
						<?=$show_comparison ?
						$this->load->view('reports/_price_over_time_comparison', '', TRUE)
						:
						$this->load->view('reports/_price_over_time', '', TRUE);
						?>
                    <?php endif; ?>
					</div>
					<?php endif;?>
					
    </div>
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script src="//interact.juststicky.com/js/embed/client.js/2028/interact_55d2374aea37e" id="interact_55d2374aea37e" data-text="Discuss this with Sticky Interact" data-unique="55d2374aea37e"></script>
<?php endif; ?>    