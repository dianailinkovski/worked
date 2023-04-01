<div class="content clear">
	<div class="actionArea clear">
		<div class="topLeft"></div>
		<div class="topRight"></div>

		<form id="srchform" method="post" action="<?=site_url('catalog')?>" class="item schedule_filter">
			<input type="hidden" id="completeURL" name="completeURL" />
			<input type="hidden" name="store_id" id="store_id" value="<?=$this->session->userdata("store_id")?>" />
			<input type="text" name="searchString" id="searchString" class="search prefill" />
		</form>

		<div class="item">
			<select id="bulkActions" name="bulkActions">
				<option value="">Selected Items</option>
				<option value="1">Delete Schedule(s)</option>
			</select>
		</div>

	</div><!-- .actionArea -->

	<div id="schedule_list"></div>

	<div class="bottomLeft"></div>
	<div class="bottomRight"></div>
</div><!-- .content -->