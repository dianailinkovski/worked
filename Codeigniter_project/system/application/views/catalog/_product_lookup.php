<div class="content clear">
    <div class="actionArea clear">
        <div class="topLeft"></div>
        <div class="topRight"></div>

        <form id="srchform" method="post" action="<?= site_url('catalog') ?>" class="item catalog_filter">
            <input type="hidden" id="completeURL" name="completeURL" />
            <input type="hidden" name="store_id" id="store_id" value="<?= $this->session->userdata("store_id") ?>" />
            <input type="text" name="searchString" id="lookupSearchString" class="search prefill" />
            <div class="button redButton resetButton"><input type="button" onclick="resetSearchFilter();" value="Reset"></div>
        </form>

        <div class="item">
            <select class="retailerLookup">
                <option value="">Select Retailer</option>
                <?php
                if (!empty($no_upc_marketplaces)):
                    foreach ($no_upc_marketplaces as $marketplace):
                        ?>
                        <option value="<?= $marketplace['id'] ?>"><?= $marketplace['display_name'] ?></option>
                        <?php
                    endforeach;
                endif;
                ?>
            </select>
        </div>

        <div class="item">
            <?php /*
            <input id="showLookupArchived" name="showArchived" type="checkbox" />
            <label for="showLookupArchived">Show Archived Items</label>
            */?>
        </div>
    </div><!-- .actionArea -->

    <div id="lookup_grid"></div>

    <div class="bottomLeft"></div>
    <div class="bottomRight"></div>
</div><!-- .content -->