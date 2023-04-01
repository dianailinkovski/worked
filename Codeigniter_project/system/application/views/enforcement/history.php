	<!-- start enforcement/merchant -->
	<?=$this->load->view('components/overview_header', array('report_name' => 'MAP Enforcement History'), TRUE)?>
	<div class=" clear"><!-- needs tabs class -->
		<?=$this->load->view('enforcement/_tabs', '', TRUE)?>
		<div id="tab1" class="tabContent">
			<div class="content clear">
				<div class="topLeft"></div>
				<div class="topRight"></div>
				<div class="whiteArea">
					<div class="topLeft"></div>
					<div class="topRight"></div>
						<section id="history">
							<div class="actionArea clear">
								<div class="topLeft"></div>
								<div class="topRight"></div>
								<div id="enforcement_grid_msg"></div>
								<form id="srchform" method="post" action="<?=site_url('enforcement')?>" class="catalog_filter">
									<label>Seller List</label>
									<input type="search" name="searchString" id="searchString" />
								</form>
			<p>Use the filter below to find specific sellers. Clicking on an item will allow you to edit additional item settings.</p>
							</div><!-- .actionArea -->
							<div id="enforcement_grid"></div>
						</section>
					<div class="bottomLeft"></div>
					<div class="bottomRight"></div>
		</div>
				<div class="bottomLeft"></div>
				<div class="bottomRight"></div>
	</div>
	</div>
	</div><!-- .tabs -->
	<!-- end enforcement/merchant -->
