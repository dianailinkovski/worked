	<!-- start enforcement/index -->
	<?=$this->load->view('components/overview_header', array('report_name' => 'MAP Enforcement Configuration'), TRUE)?>
	<div class=" clear">
		<?=$this->load->view('enforcement/_tabs', '', TRUE)?>
		<div id="tab1" class="tabContent">
			<div class="content clear">
				<div class="topLeft"></div>
				<div class="topRight"></div>
				<div class="whiteArea">
					<div class="topLeft"></div>
					<div class="topRight"></div>
						<section id="settings">
							<form action="<?=site_url('enforcement/edit/'.$template_id)?>" method="post" id="enforcement_form" class="clear">
								<input type="hidden" name="template_id" id="template_id" value="<?=$template_id;?>" />
								

								<h1>Edit Email Settings</h1><br>

								<?php if (!empty($message)) echo '<div class="message error">'.$message.'</div>';?>

								
                               <div class="thirdCol">
									<h2><?=convert_number_to_name($notification_level)?> Warning Email</h2>
									<div class="inputBlockContainer">
										<label for="subject">Subject:</label>

											<input type="text" name="subject" id="subject" class="validate[required,minSize[5],maxSize[255] medium" value="<?=set_value('subject', $subject);?>" maxlength="255">
									</div>
									<div class="inputBlockContainer">
										<label for="notify_after_days">Notify after:</label>

											<input type="text" name="notify_after_days" id="notify_after_days" value="<?=set_value('notify_after_days', $notify_after_days);?>" size="5"> day(s) after first warning expires
									</div>
									<div class="inputBlockContainer">
										<label for="no_of_days_to_repeat">Repeat for:</label>

											<input type="text" name="no_of_days_to_repeat" id="no_of_days_to_repeat" value="<?=set_value('no_of_days_to_repeat', $no_of_days_to_repeat);?>" size="5"> day(s)
									</div>
								</div>
                            
                                <div style="clear: both;"></div>
                                
								<div class="button redButton">
									<div class="buttonCornerL"></div>
									<input id="edit_store" name="submit" type="submit" value="Save" class="">
								</div>
							</form>
						</section>
					<div class="bottomLeft"></div>
					<div class="bottomRight"></div>
				</div>
				<div class="bottomLeft"></div>
				<div class="bottomRight"></div>
			</div>
		</div>
	</div><!-- .tabs -->
	<!-- end enforcement/index -->
