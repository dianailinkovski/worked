	<!-- start enforcement/_tabs -->
	<ul class="tabNav clear">
        <li<?=($method === 'index') ? ' class="ui-tabs-selected ui-state-active"' : '';?>><div class="tabCornerL"></div><a href="<?=site_url('enforcement')?>" class="tabItem">Edit Store</a></li>
        <li<?=($method === 'settings') ? ' class="ui-tabs-selected ui-state-active"' : '';?>><div class="tabCornerL"></div><a href="<?=site_url('enforcement/settings')?>" class="tabItem">Enforcement Settings</a></li>
		<li<?=($method === 'email_settings' || $method === 'edit') ? ' class="ui-tabs-selected ui-state-active"' : '';?>><div class="tabCornerL"></div><a href="<?=site_url('enforcement/email_settings')?>" class="tabItem">Email Settings</a></li>
		<li<?=($method === 'merchant') ? ' class="ui-tabs-selected ui-state-active"' : '';?>><div class="tabCornerL"></div><a href="<?=site_url('enforcement/merchant')?>" class="tabItem">Merchant Info</a></li>
		<li<?=($method === 'templates' || $method === 'template') ? ' class="ui-tabs-selected ui-state-active"' : '';?>><div class="tabCornerL"></div><a href="<?=site_url('enforcement/templates')?>" class="tabItem">Email Templates</a></li>
		<li<?=($method === 'amazone_violator') ? ' class="ui-tabs-selected ui-state-active"' : '';?>><div class="tabCornerL"></div><a href="<?=site_url('enforcement/amazone_violator')?>" class="tabItem">Marketplace Logins</a></li>
	</ul>
	<!-- end enforcement/_tabs -->