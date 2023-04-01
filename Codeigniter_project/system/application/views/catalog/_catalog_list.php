<div class="content clear">
    <div class="actionArea clear">

        <form id="srchform" method="post" action="<?= site_url('catalog') ?>" class="item catalog_filter">
            <input type="hidden" id="completeURL" name="completeURL" />
            <input type="hidden" name="store_id" id="store_id" value="<?= $this->session->userdata("store_id") ?>" />
            <input type="text" name="searchString" id="searchString" class="search prefill" />
            <div class="button redButton resetButton"><input type="button" onclick="resetSearchFilter();" value="Reset"></div>
        </form>

        <div class="item">
            <select class="bulkActions" id="catalog_action">
                <option value="">Bulk Action</option>
                <option value="2">Track Product(s)</option>
                <option value="3">Un-Track Product(s)</option>
                <option value="4">Archive Product(s)</option>
                <option value="5">Un-Archive Product(s)</option>
            </select>
        </div>
        <div class="item">
            <input id="showArchivedCatalogList" class="showArchived" name="showArchived" type="checkbox" />
            <label for="showArchivedCatalogList">Show Archived Items</label>
        </div>
        <div class="item add_item">
            <strong>+</strong>&nbsp;<a href="javascript:void(0);" onclick="addProductPopup('prodAdd');">Add Item</a>
        </div>
        <div class="itemRight">
            <?= $this->load->view('components/save_options', '', TRUE); ?>
        </div>
    </div>

    <div id="brand_catalog" class="catGrid"></div>

</div>