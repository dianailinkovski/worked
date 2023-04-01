<div class="content clear">
    <div class="actionArea clear">
        <div class="topLeft"></div>
        <div class="topRight"></div>

        <form id="srchform" method="post" action="<?= site_url('catalog/pricing') ?>" class="item catalog_filter">
            <input type="hidden" id="completeURL" name="completeURL" />
            <input type="hidden" name="store_id" id="store_id" value="<?= $store_id ?>" />
            <input type="text" name="priceSearchString" id="priceSearchString" class="search prefill" />
            <div class="button redButton resetButton"><input type="button" onclick="resetPriceFilter();" value="Reset"></div>
        </form>

        <div class="item">
            <form id="pricingSwitchForm" action="<?= site_url('catalog') ?>" method="post">
                <select id="pricingSwitch" name="price_type">
                    <!-- <option value="">Bulk Action</option> -->
                    <option value="price_floor" <?= $price_type === 'price_floor' ? 'selected="selected"' : '' ?>>MAP</option>
                    <option value="retail_price" <?= $price_type === 'retail_price' ? 'selected="selected"' : '' ?>>Retail</option>
                    <option value="wholesale_price" <?= $price_type === 'wholesale_price' ? 'selected="selected"' : '' ?>>Wholesale</option>
                </select>
            </form>
        </div>

        <div class="item">
            <span id="addPromoPricing" class="jsLink">+Add Record</span>
        </div>

        <div class="item">
            <span id="deletePromoPricing" class="jsLink">-Delete Record</span>
        </div>

    </div>

    <div id="promo_pricing"></div>

    <div class="bottomLeft"></div>
    <div class="bottomRight"></div>
</div>

<div id="addPriceHistory" class="modalWindow dialog">
    <form id="addPriceForm">
        <h2>Add Promotional Pricing <?= !empty($price_column->display_name) ? ' - ' . ($price_column->display_name != 'Floor Price' ? $price_column->display_name : 'MAP') : '' ?></h2>
        <span id="addPriceMessage" class="hidden"></span>
        <div class="row_dat">
            <div class="lbel">Title</div>
            <div class="lbl_inpuCnt">
                <input type="text" name="associated_product_title" id="associated_product_title" value="" />
            </div>
            <div id="assocTitleAutoComplete"></div>
        </div>
        <div class="row_dat">
            <div class="lbel">UPC</div>
            <div class="lbl_inpuCnt"><input type="text" name="associated_upc" id="associated_upc" value="" /></div>
            <input type="hidden" name="associated_product_id" id="associated_product_id" value="" />
            <input type="hidden" name="pricing_type" value="<?= $price_type ?>" />
            <div id="assocUpcAutoComplete"></div>
        </div>
        <div class="row_dat">
            <div class="lbel">Price:</div>
            <div class="lbl_inpuCnt">
                <input type="text" name="price_value" id="price_value" />
                <div class="clear"></div>
            </div>
        </div>
        <div class="row_dat">
            <div class="lbel">Start:</div>
            <div class="lbl_inpuCnt">
                <input type="text" name="price_start" id="price_start" />
                <div class="clear"></div>
            </div>
        </div>
        <div class="row_dat">
            <div class="lbel">End:</div>
            <div class="lbl_inpuCnt"><input type="text" name="price_end" id="price_end" /></div>
            <div class="clear"></div>
        </div>
    </form>
</div><!-- end #addPriceHistory -->