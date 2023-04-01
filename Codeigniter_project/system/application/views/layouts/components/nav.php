<?php if (isset($this->logged_in)): ?>
    <nav role="navigation" class="clear">
    	<div class="container">
    		<img src="images/nav/sticky-vision.png" alt="TrackStreet" width="99" height="29">
    		<?php
    		if(isset($this->permission_id) && ($this->permission_id == '0')):?> 
    			<ul class="tabNav clear">
    			  <li class="dropdown ui-tabs-selected ui-state-active">
    				<div class="tabCornerL"></div>
    				<a class="tabItem" href="">SuperAdmin</a>
    				<div class="dropdownMenu">
    				  <div class="dropdownBg">
    					<a href="<?=site_url('category')?>" style="text-transform: capitalize;">Categories</a>
    					<a href="<?=site_url('srvchk/status.php')?>" style="text-transform: capitalize;">Process Check</a>
    					<a href="<?=site_url('whackprices')?>" style="text-transform: capitalize;">Whack Prices</a>
    					<a href="<?=site_url('crawlers')?>" style="text-transform: capitalize;">Crawlers</a>
    				  </div>
    				</div>
    			  </li>
    			</ul> &nbsp;|&nbsp;
    		<?php endif;?>
    		<a href="<?=site_url()?>" <?=$controller === 'overview' ? 'class="selected"' : ''?>>Overview</a> &nbsp;|&nbsp;
    		<a href="<?=site_url('reports')?>" <?=$controller === 'reports' ? 'class="selected"' : ''?>>Reports</a> &nbsp;|&nbsp;
    		<?php
    		if(isset($this->permission_id) && ($this->permission_id == '0' || $this->permission_id == '2')):?>
    			<a href="<?=site_url('catalog')?>" <?=$controller === 'catalog' ? 'class="selected"' : ''?>>Catalog</a> &nbsp;|&nbsp;
    			<!-- <a href="<?=site_url('settings/edit_store/')?>" <?=$controller === 'settings' ? 'class="selected"' : ''?>>Settings</a> &nbsp;|&nbsp; -->
    		<?php endif;?>
    		<a href="<?=site_url('schedule')?>" <?=$controller === 'schedule' ? 'class="selected"' : ''?>>Schedule</a>
    		<?php
    		if(isset($this->permission_id) && ($this->permission_id == '0' || $this->permission_id == '2')):?> &nbsp;|&nbsp;
    			<a href="<?=site_url('enforcement')?>" <?=$controller === 'enforcement' ? 'class="selected"' : ''?>>MAP Enforcement</a>
    		<?php endif;?>
    	</div>
    </nav>
<?php endif; ?>
