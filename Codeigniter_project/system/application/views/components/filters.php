<section class="clear select_report fl<?= $display ? '' : ' hidden' ?>" id="filter_result">
    <div class="leftCol">
        <label>Filter Result?</label>
    </div>
    <div class="rightCol">
        <input type="hidden" id="report_type" name="report_type" value="<?= $report_type ?>" />
        <input type="hidden" id="by_which" name="by_which" value="<?= (isset($by_which)) ? $by_which : ''; ?>" />
        <input type="hidden" id="report_id" name="report_id" value="<?= empty($report_id) ? 0 : $report_id ?>" />
        <input type="hidden" id="report_name" name="report_name" value="<?= $report_name ?>" />

        <?php
//		$marketBoxTxt = 'MARKETS/RETAILERS';
        if ($by_which !== 'bymarket'):
            $allMarketsChecked = '';
            $allRetailersChecked = '';
            if ($all_markets OR !$submitted)
            {
                $allMarketsChecked = ' checked="checked"';
                //if ( ! $all_retailers)
                //$marketBoxTxt = 'MARKETS';
            }
            if ($all_retailers OR !$submitted)
            {
                $allRetailersChecked = ' checked="checked"';
                //if ( ! $all_markets)
                //$marketBoxTxt = 'RETAILERS';
            }
            if ($all_retailers AND $all_markets OR !$submitted)
            {
                //$marketBoxTxt = 'MARKETS/RETAILERS';
            }
            else
            {
                //$individuals = ucwords(implode(', ', array_map('marketplace_display_name', $markets)));
                //$marketBoxTxt = isset($marketBoxTxt) ? $marketBoxTxt . ', ' . $individuals : $individuals;
                //if (strlen($marketBoxTxt) > 20) {
                //$marketBoxTxt = substr($marketBoxTxt, 0, 20) . '...';
                //}
            }
            ?>
            <div class="selectMenu">
                <div class="selectMenuToggle">MARKETS/RETAILERS</div>
                <div class="selectMenuDropdown">
                    <div class="inputContainer">
                        <input type="checkbox" name="all_markets" value="all"<?= $allMarketsChecked ?> class="all_marketplace" />ALL MARKETS
                    </div>
    <?php
    foreach ($marketplaceArr as $val):
        $checked = (is_array($markets) && in_array($val, $markets)) ? ' checked="checked"' : '';
        ?>
                        <div class="inputContainer">
                            <input type="checkbox" name="markets[]" value="<?= $val ?>"<?= $checked ?> class="marketplace" /><?= marketplace_display_name($val) ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!empty($retailerArr)): ?>
                        <div class="inputContainer">
                            <input type="checkbox" name="all_retailers" value="all"<?= $allRetailersChecked ?> class="all_retailer" />ALL RETAILERS
                        </div>
        <?php
        foreach ($retailerArr as $val):
            $checked = (is_array($markets) && in_array($val, $markets)) ? ' checked="checked"' : '';
            ?>
                            <div class="inputContainer">
                                <input type="checkbox" name="markets[]" value="<?= $val ?>"<?= $checked ?> class="retailer" /><?= marketplace_display_name($val) ?>
                            </div>
                <?php endforeach;
            endif;
            ?>
                </div>
            </div>
<?php endif; ?>

<?php
if ($by_which !== 'bymerchant') :
    $allMerchantsChecked = (empty($merchants)) ? ' checked="checked"' : '';
    ?>
            <div class="selectMenu">
                <div class="selectMenuToggle">MERCHANTS</div>
                <div class="selectMenuDropdown">
                    <div class="inputContainer">
                        <input type="checkbox" value="all"<?= $allMerchantsChecked; ?> class="all_merchant" />ALL MERCHANTS
                    </div>
                    <?php
                    foreach ($proMerchants as $mdata) :
                        $name = $mdata->merchant_name;
                        $org_name = trim($mdata->original_name);
                        $marketplace = getUniqueKeywordsFromString($mdata->market_place);
                        $checked = (is_array($merchants) && in_array($mdata->id, $merchants)) ? ' checked="checked"' : '';
                        ?>
                        <div class="inputContainer">
                            <input type="checkbox" name="merchants[]" value="<?= $mdata->id; ?>"<?= $checked; ?> class="merchant" /><?= trim($org_name); ?>
                        </div>
    <?php endforeach; ?>
                </div>
            </div>
<?php endif; ?>

        <input name="apply" id="apply" type="button" value="Apply" onclick="submitReportForm();" class="btn btn-success">
    </div>
</section><!-- #filter_result -->
