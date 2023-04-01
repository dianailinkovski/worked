	<!-- start enforcement/staff_notes -->
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
								<label>Staff Notes</label>
							</div><!-- .actionArea -->
							<div id="staff_notes_grid"></div>
						</section>
					<div class="bottomLeft"></div>
					<div class="bottomRight"></div>
                </div>
				<div class="bottomLeft"></div>
				<div class="bottomRight"></div>
            </div>
        </div>
	</div><!-- .tabs -->
	<!-- end enforcement/staff_notes -->
