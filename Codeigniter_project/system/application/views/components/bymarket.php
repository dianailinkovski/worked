<?php
$allMarketsChecked = '';
$allRetailersChecked = '';
if ($all_markets OR !$submitted)
{
    $allMarketsChecked = ' checked="checked"';
    if (!$all_retailers)
        $marketBoxTxt = 'ALL MARKETS';
}
if ($all_retailers OR !$submitted)
{
    $allRetailersChecked = ' checked="checked"';
    if (!$all_markets)
        $marketBoxTxt = 'ALL RETAILERS';
}
if ($all_retailers AND $all_markets OR !$submitted)
{
    $marketBoxTxt = 'ALL MARKETS/RETAILERS';
}
else
{
    $individuals = is_array($markets) ? ucwords(implode(', ', array_map('marketplace_display_name', $markets))) : "";
    $marketBoxTxt = isset($marketBoxTxt) ? $marketBoxTxt . ', ' . $individuals : $individuals;
    if (strlen($marketBoxTxt) > 20)
    {
        $marketBoxTxt = substr($marketBoxTxt, 0, 20) . '...';
    }
}
?>
<!-- start _bymarket -->
<div class="clear select_report<?= ($display ? '' : ' hidden') . ($is_first ? ' search_first' : ''); ?>" id="market_container">
    <div class="leftCol">
        <label>Select Markets/Retailers</label>
    </div>
    <div class="rightCol">
        <div class="selectMenu">
            <div class="selectMenuToggle"><?= $marketBoxTxt ?></div>
            <div class="selectMenuDropdown">
                <div class="inputContainer">
                    <input type="checkbox" name="all_markets" value="all"<?= $allMarketsChecked; ?> class="all_market" />ALL MARKETS
                </div>
                <?php
                foreach ($marketplaceArr as $val) :
                    $checked = (is_array($markets) && in_array($val, $markets)) ? ' checked="checked"' : '';
                    ?>
                    <div class="inputContainer">
                        <input type="checkbox" name="markets[]" value="<?= $val; ?>"<?= $checked; ?> class="market" /><?= marketplace_display_name($val); ?>
                    </div>
                <?php endforeach; ?>
                <div class="inputContainer">
                    <input type="checkbox" name="all_retailers" value="all"<?= $allRetailersChecked; ?> class="all_retailer" />ALL RETAILERS
                </div>
                <?php
                foreach ($retailerArr as $val) :
                    $checked = (is_array($markets) && in_array($val, $markets)) ? ' checked="checked"' : '';
                    ?>
                    <div class="inputContainer">
                        <input type="checkbox" name="markets[]" value="<?= $val; ?>"<?= $checked; ?> class="retailer" /><?= marketplace_display_name($val); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div><!-- end #market_container -->
