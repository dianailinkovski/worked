	<!-- start enforcement/merchant -->
	<?=$this->load->view('components/overview_header', array('report_name' => 'MAP Enforcement Emails'), TRUE)?>
	<div class=" clear"><!-- needs tabs class -->
		<?=$this->load->view('enforcement/_tabs', '', TRUE)?>
		<div id="tab1" class="tabContent">
			<div class="content clear">
				<div class="topLeft"></div>
				<div class="topRight"></div>
				<div class="whiteArea">
					<div class="topLeft"></div>
					<div class="topRight"></div>
						<section id="emails">

							<form action="<?=site_url('enforcement/emails')?>" method="post" id="enforcement_form" name="enforcement_form">

									<?php if ( ! empty($message)) echo '<div class="message error">'.$message.'</div>';?>

									<div id="emailTabs" class="tabs subTabs tabsYellow">
										<ul class="tabNav clear">
											<li><div class="tabCornerL"></div><a href="#letter-1-known" class="tabItem">Letter #1 (For known sellers)</a></li>
											<li><div class="tabCornerL"></div><a href="#letter-2-known" class="tabItem">Letter #2 (For known sellers)</a></li>
											<li><div class="tabCornerL"></div><a href="#letter-1-unknown" class="tabItem">Letter #1 (For unknown sellers)</a></li>
											<li><div class="tabCornerL"></div><a href="#letter-2-unknown" class="tabItem">Letter #2 (For unknown sellers)</a></li>
										</ul>
										<section id="letter-1-known" class="tabContent">
											<div class="emailLegend">
												<h4>Wildcard Text</h4>
												<p>#SellerName<br>
												#CompanyName<br>
												#ContactName<br>
												#Phone<br>
												<!-- #ProductName --></p>
											</div>
											<textarea id="wysiwyg1" class="wysiwyg" name="html_body[known_seller_1]" name="html_body[known_seller_1]"><?=set_value('html_body[known_seller_1]', $this->data->html_body_known_seller_1);?></textarea>
											<?=form_error('html_body[known_seller_1]');?>
											<input type="hidden" name="id[known_seller_1]" id="id_known_seller_1" value="<?=$this->data->id_known_seller_1;?>" />
										</section>
										<section id="letter-2-known" class="tabContent hidden">
											<div class="emailLegend">
												<h4>Wildcard Text</h4>
												<p>#SellerName<br>
												#CompanyName<br>
												#ContactName<br>
												#Phone<br>
												<!-- #ProductName --></p>
											</div>
											<textarea id="wysiwyg2" class="wysiwyg" name="html_body[known_seller_2]" name="html_body[known_seller_2]"><?=set_value('html_body[known_seller_2]', $this->data->html_body_known_seller_2);?></textarea>
											<?=form_error('html_body[known_seller_2]');?>
											<input type="hidden" name="id[known_seller_2]" id="id_known_seller_2" value="<?=$this->data->id_known_seller_2;?>" />
										</section>
										<section id="letter-1-unknown" class="tabContent hidden">
											<div class="emailLegend">
												<h4>Wildcard Text</h4>
												<p>#SellerName<br>
												#CompanyName<br>
												#ContactName<br>
												#Phone<br>
												<!-- #ProductName --></p>
											</div>
											<textarea id="wysiwyg3" class="wysiwyg" name="html_body[unknown_seller_1]" name="html_body[unknown_seller_1]"><?=set_value('html_body[unknown_seller_1]', $this->data->html_body_unknown_seller_1);?></textarea>
											<?=form_error('html_body[unknown_seller_1]');?>
											<input type="hidden" name="id[unknown_seller_1]" id="id_unknown_seller_1" value="<?=$this->data->id_unknown_seller_1;?>" />
										</section>
										<section id="letter-2-unknown" class="tabContent hidden">
											<div class="emailLegend">
												<h4>Wildcard Text</h4>
												<p>#SellerName<br>
												#CompanyName<br>
												#ContactName<br>
												#Phone<br>
												<!-- #ProductName --></p>
											</div>
											<textarea id="wysiwyg4" class="wysiwyg" name="html_body[unknown_seller_2]" name="html_body[unknown_seller_2]"><?=set_value('html_body[unknown_seller_2]', $this->data->html_body_unknown_seller_2);?></textarea>
											<?=form_error('html_body[unknown_seller_2]');?>
											<input type="hidden" name="id[unknown_seller_2]" id="id_unknown_seller_2" value="<?=$this->data->id_unknown_seller_2;?>" />
										</section>
									</div><!-- .tabs -->

									<div class="button redButton">
										<div class="buttonCornerL"></div>
										<input name="submit" type="submit" value="Save">
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
	<!-- end enforcement/merchant -->