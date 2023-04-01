<!-- start violations/reports_view -->
	<?=$this->load->view('components/overview_header', '', TRUE)?>
	<div class=" clear"><!-- needs tabs class -->
		<?=$this->load->view('components/reports_header', '', TRUE)?>
		<div id="tab1" class="tabContent">
			<div class="content clear">
				<div class="topLeft"></div>
				<div class="topRight"></div>
				<div class="whiteArea">
					<form method="post" name="reportfrm" id="reportfrm" action="<?=site_url('violations/' . $report)?>">
						<input type="hidden" name="formSubmit" value="1">
						<?php
						foreach ($optArray as $comp => $opts):
							echo $this->load->view('components/' . $comp, $opts, TRUE);
						endforeach;
						?>
					</form>

					<div class="product_movement">
                    <?php if ( ! empty($Data)): ?>
						<div class="report_title"><?=$report_name;?></div>
						<?=$this->load->view('components/save_options', '', TRUE);?>
						<div class="clear"></div>
                        //<?=$this->load->view('components/chart', array('gData', $gData), TRUE);?>
                    <?php elseif ( isset($date_from) && isset($date_to) && isset($gData) ) : ?>                        
                        <?=$this->load->view('components/chart', array('gData', $gData), TRUE);?>
                    <?php endif; ?>
					</div>
					
					<?php if ($submitted):?>
					<div class="bottom_tables">
						<?=$show_comparison ?
						$this->load->view('violations/_price_over_time_comparison', '', TRUE)
						:
						$this->load->view('violations/_price_over_time', '', TRUE);
						?>
					</div>
					<?php endif;?>
				</div>
				<div class="bottomLeft"></div>
				<div class="bottomRight"></div>
			</div>
		</div>
	</div>
<!-- end violations/reports_view -->