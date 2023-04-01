<style type="text/css">
    #spanElement {
        max-width: 546px;
    }
</style>
<div class="productActionArea clear">
    <div class="whiteArea groupArea">
        <div id="productGroupList" class="hidden"></div>
    </div>
    <div class="whiteArea groupArea last">
        <div id="noGroupSelected" class="hidden">Please select a group.</div>
        <div id="groupProductList" class="hidden"></div>
    </div>
</div>
<div class="content clear">
    <div class="actionArea clear">
        <div class="topLeft"></div>
        <div class="topRight"></div>

        <form id="srchform" method="post" action="<?= site_url('catalog') ?>" class="item catalog_filter">
            <input type="hidden" id="completeURL" name="completeURL" />
            <input type="hidden" name="store_id" id="store_id" value="<?= $this->session->userdata("store_id") ?>" />
            <input type="text" name="searchString" id="searchString" class="search prefill" />
            <div class="button redButton resetButton"><input type="button" onclick="resetSearchFilter();" value="Reset"></div>
        </form>

        <div class="item">
            <select class="bulkActions" id="productGroupActions">
                <option value="">Bulk Action</option>
                <?php if ($store_id !== 'all'): ?>
                    <option value="group_add">Create Group</option>
                    <option value="group_product_add">Add To Group</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="item">
            <input id="showArchivedProductGroups" class="showArchived" name="showArchived" type="checkbox" />
            <label for="showArchivedProductGroups">Show Archived Items</label>
        </div>

        <div class="itemRight">
            <?= $this->load->view('components/save_options', '', TRUE); ?>
        </div>
    </div><!-- .actionArea -->

    <div id="productGroupsBody" class="catGrid"></div>

    <div class="bottomLeft"></div>
    <div class="bottomRight"></div>
</div>

<div id="addGroupPopup" class="modalWindow dialog">
    <form name="newGroupForm" id="newGroupForm">
        <h2>Create Group</h2>
        <div id="createGroupMessage"></div>
        <div class="row_dat">
            <div class="lbel">Enter Group Name:</div>
            <div class="lbl_inpuCnt">
                <input type="hidden" name="group_ids" id="group_ids" value="" />
                <input type="text" name="group_name" id="group_name" maxlength="30" />
            </div>
            <div class="clear"></div>
        </div>
    </form>
</div><!-- end #addGroupPopup -->
