<?php 
$allMerchantsChecked = (empty($merchants)) ? ' checked="checked"' : '';
?>
<section class="clear select_report<?= ($display ? '' : ' hidden') . ($is_first ? ' search_first' : ''); ?>" id="merchant_container">
    <div class="leftCol">
        <label>Select Merchants</label>
    </div>
    <div class="rightCol">
        <div class="selectMenu">
            <div class="selectMenuToggle">Merchants</div>
            <div class="selectMenuDropdown">
                <div class="inputContainer">
                    <input type="checkbox" value="all"<?= $allMerchantsChecked; ?> class="all_merchant" />ALL MERCHANTS
                </div>
                <?php
                foreach ($proMerchants as $mdata) :
                    $name = $mdata->merchant_name;
                    $org_name = trim($mdata->original_name);
                    $marketplace = getUniqueKeywordsFromString($mdata->market_place);
                    $checked = (isset($merchants) && is_array($merchants) && in_array($mdata->id, $merchants)) ? ' checked="checked"' : '';
                    ?>
                    <div class="inputContainer">
                        <input type="checkbox" name="merchants[]" value="<?= $mdata->id; ?>"<?= $checked; ?> class="merchant" /><?= trim($org_name); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section><!-- end #merchant_container -->
