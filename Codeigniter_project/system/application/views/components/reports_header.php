<!-- start reports_header -->
<ul class="tabNav clear">
    <li<?= ($controller === 'overview' || trim($controller) === "") ? ' class="ui-tabs-selected ui-state-active"' : ''; ?>><div class="tabCornerL"></div><a href="<?= site_url('overview') ?>" class="tabItem">Pricing Overview</a></li>
    <li class="dropdown<?= ($controller == 'reports') ? ' ui-tabs-selected ui-state-active' : ''; ?>">
        <div class="tabCornerL"></div>
        <a href="<?= site_url('reports') ?>" class="tabItem">Pricing Over Time</a>
        <div class="dropdownMenu">
            <div class="dropdownBg">
                <a href="<?= site_url('reports') ?>">By Product</a>
                <a href="<?= site_url('reports/bymerchant') ?>">By Merchant</a>
                <a href="<?= site_url('reports/bymarket') ?>">By Market</a>
                <a href="<?= site_url('reports/bygroup') ?>">By Group</a>
                <a href="<?= site_url('reports/bycompetition') ?>">By Competition</a>
            </div>
        </div>
    </li>
    <li class="dropdown<?= ($controller === 'violations' OR $controller === 'violationoverview') ? ' ui-tabs-selected ui-state-active' : ''; ?>">
        <div class="tabCornerL"></div>
        <a href="<?= site_url('violationoverview') ?>" class="tabItem">Price Violations</a>
        <?php /************
        <div class="dropdownMenu">
            <div class="dropdownBg">
                <a href="<?= site_url('violations/bydate') ?>">By Date</a>
                <a href="<?= site_url('violations/byproduct') ?>">By Product</a>
                <a href="<?= site_url('violations/bymerchant') ?>">By Merchant</a>
                <a href="<?= site_url('violations/bymarket') ?>">By Market</a>
                <a href="<?= site_url('violations/bygroup') ?>">By Group</a>
            </div>
        </div>
        *********************/ ?>
    </li>
    <li<?= ($controller === 'whois') ? ' class="ui-tabs-selected ui-state-active"' : ''; ?>><div class="tabCornerL"></div><a href="<?= site_url('whois') ?>" class="tabItem">Who's Selling My Products</a></li>
    <li<?= ($controller === 'savedreports') ? ' class="ui-tabs-selected ui-state-active"' : ''; ?>><div class="tabCornerL"></div><a href="<?= site_url('savedreports') ?>" class="tabItem"> Saved Reports</a></li>
</ul>
<!-- end reports_header -->

