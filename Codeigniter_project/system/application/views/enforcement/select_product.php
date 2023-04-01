<?=$this->load->view('components/overview_header', array('report_name' => 'Schedule Reports:'), TRUE);?>

<div class="tabs bigTabs clear">
	<section id="productList" class="tabContent">
		<div class="content clear">
			<div class="actionArea clear">
				<div class="topLeft"></div>
				<div class="topRight"></div>
	
				<form id="srchform" method="post" action="<?=site_url('enforcement/select_product')?>" class="item schedule_filter">
					<input type="hidden" id="completeURL" name="completeURL" /> 
					<input type="text" name="searchString" id="searchString" class="search prefill" />
					<div class="button redButton resetButton">
						<input type="button" value="Reset">
					</div>
				</form>
			</div>
			<!-- .actionArea -->
	
			<div id="product_list"></div>
	
			<div class="bottomLeft"></div>
			<div class="bottomRight"></div>
		</div>
		<!-- .content --> 
	</section>
</div>
