                    <div id="merchant_staff_notes_edit_dialog" class="modalWindow dialog">
						<div class="dlg-content">
							<div class="whiteArea">
								<div class="topLeft"></div>
								<div class="topRight"></div>
								
								<h2>
									Edit comment
								</h2>
							
								<div class="merchant_staff_note_write">
									<div id="merchant_staff_note_edit_error" style="display:none;"></div>
									<form id="frm_edit_note_of_merchant" method="post" action="<?=site_url('enforcement/write_note_of_merchant')?>" class="clear">
                                        <textarea id="entry_note" style="margin-top: 5px; width: 100%; height: 80px" title="Add your comment here" placeholder="Add your comment here"></textarea>
									</form>
								</div>
								<div class="bottomLeft"></div>
								<div class="bottomRight"></div>
							</div>
						</div>						
					</div>
					<!-- End dialog of merchant note list -->