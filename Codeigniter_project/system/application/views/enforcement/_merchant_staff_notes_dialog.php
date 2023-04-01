                    <div id="merchant_staff_notes_dialog" class="modalWindow dialog">
						<div class="dlg-content">
							<div class="actionArea clear">
								<input type="text" id="searchStaffNotesString" class="search">
								<div class="button redButton resetButton">
									<input type="button" value="Search" onclick="get_staff_note_of_merchant(0)">
								</div>
                                
                                &nbsp;&nbsp;&nbsp;
                                <select id="staffNotesActions" onchange="staff_note_action($(this))">
                                    <option value="">Select action</option>
                                    <option value="all">All select</option>
                                    <option value="unall">All un select</option>
                                    <option value="delete">Delete selected items</option>
                                </select>
                                
								<input type="hidden" id="merchant_name_id" />
							</div>
							<div class="merchant_staff_notes_list">
                				<div id="merchant_staff_notes_list"></div>
                				<div id="merchant_staff_notes_loading"></div>
              				</div>
							
                            <?php if ( isset($permission_id) && ($permission_id == 0 || $permission_id == 2) ): ?>
							<div class="whiteArea" style="margin-top: 25px;">
								<div class="topLeft"></div>
								<div class="topRight"></div>
								
								<h2>
									Write a new comment about <span id="note_merchant_name"></span>
								</h2>
							
								<div class="merchant_staff_note_write">
									<div id="merchant_staff_note_error" style="display:none;"></div>
									<form id="frm_note_of_merchant" method="post" action="<?=site_url('enforcement/write_note_of_merchant')?>" class="clear">
                                        <textarea id="entry_note" style="margin-top: 5px; width: 100%; height: 80px" title="Add your comment here" placeholder="Add your comment here"></textarea>
									</form>
								</div>
								<div class="bottomLeft"></div>
								<div class="bottomRight"></div>
							</div>
                            <?php endif; ?>
						</div>						
					</div>
					<!-- End dialog of merchant note list -->