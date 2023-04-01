<div id="merchant_notes_dialog" class="modalWindow dialog">
	<div class="dlg-content">
	  <!--  
		<div class="actionArea clear">
			<input type="text" id="searchDiscussionString" class="search">
			<input class="btn btn-success" type="button" value="Search" onclick="get_note_of_merchant(0)">
			<input type="hidden" id="merchant_name_id" />
		</div>
		<div class="merchant_notes_list">
			<div id="merchant_notes_list"></div>
			<div id="merchant_notes_loading"></div>
		</div>
		-->
		
		<div class="whiteArea" style="margin-top: 25px;">
			<div class="topLeft"></div>
			<div class="topRight"></div>
			
			<h2>
				Create a new comment about <span id="note_merchant_name"></span>
			</h2>
		
			<div class="merchant_note_write">
				<div id="merchant_note_error" style="display:none;"></div>
				<form id="frm_note_of_merchant" method="post" action="<?=site_url('enforcement/write_note_of_merchant')?>" class="clear">
					<div>
						<label for="type_of_entry">Post Type:&nbsp;</label>
						<select id="type_of_entry" title="Type of Entry" >
							<option value="New Information">New Information</option>
							<option value="Suspected Source">Suspected Source</option>
							<option value="Identified Source">Identified Source</option>
						</select>
					</div>

					<textarea id="entry_note" style="margin-top: 5px; width: 100%; height: 80px" title="Add your comment here" placeholder="Add your comment here"></textarea>
				</form>
			</div>
			<div class="bottomLeft"></div>
			<div class="bottomRight"></div>
		</div>
	</div>						
</div>
<!-- End dialog of merchant note list -->