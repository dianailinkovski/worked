<div class="productActionArea">
    <div class="whiteArea">
        <a class="competitor-add-prod"><img src="/images/icons/16/13.png" alt="Add" width="16" height="16" border="0" class="linkIcon">Add New Competing Product</a>
        <a class="competitor-del-prod"><img src="/images/icons/16/69.png" alt="Delete" width="16" height="16" border="0" class="linkIcon">Delete Competing Product(s)</a>
        <a class="competitor-ass-prod"><img src="/images/icons/link.png" alt="Link" width="23" height="23" border="0" class="linkIcon">Link Your Product</a>
        <a class="competitor-unass-prod"><img src="/images/icons/unlink.png" alt="Unlink" width="23" height="23" border="0" class="linkIcon">Unlink Your Product</a>
    </div>
</div>
<div class="content clear">
    <div class="actionArea clear">
        <div class="topLeft"></div>
        <div class="topRight"></div>
        <form id="comp_srchform" method="post" action="<?= site_url('catalog') ?>" class="catalog_filter item">
            <input type="hidden" />
            <input type="hidden" value="<?= $this->session->userdata("store_id") ?>" />
            <input id="compSearchString" name="compSearchString" type="text" class="search prefill" />
            <div class="button redButton resetButton"><input type="button" onclick="resetCompFilter();" value="Reset"></div>
        </form>
    </div>
    <div id="competitor_grid"></div>
    <div class="bottomLeft"></div>
    <div class="bottomRight"></div>
</div>



<div id="addCompPopup" class="modalWindow dialog">
    <form name="addCompProdFrm" id="addCompProdFrm">
        <h2>Add New Competing Product</h2>
        <span id="add_comp_product_error" class="error"></span>
        <div class="row_dat">
            <div class="lbel">Title</div>
            <div class="lbl_inpuCnt">
                <input type="text" name="competitor_product_title" id="competitor_product_title" value="" />
            </div>
            <div id="compTitleAutoComplete"></div>
            <div class="clear"></div>
        </div>
        <div class="row_dat">
            <div class="lbel">UPC</div>
            <div class="lbl_inpuCnt">
                <input type="text" name="competitor_upc" id="competitor_upc" value="" />
                <input type="hidden" name="competitor_product_id" id="competitor_product_id" value="" />
            </div>
            <div id="compUpcAutoComplete"></div>
            <div class="clear"></div>
        </div>
    </form>
</div><!-- end #addCompPopup -->

<div id="assCompPopup" class="modalWindow dialog">
    <form name="assCompProdFrm" id="assCompProdFrm">
        <h2>Associate Competing Product</h2>
        <span id="ass_comp_product_error" class="error"></span>
        <div class="row_dat">
            <div class="lbel">Title</div>
            <div class="lbl_inpuCnt">
                <input type="text" name="associated_product_title" id="associated_product_title" value="" />
            </div>
            <div id="assocTitleAutoComplete"></div>
            <div class="clear"></div>
        </div>
        <div class="row_dat">
            <div class="lbel">UPC</div>
            <div class="lbl_inpuCnt">
                <input type="text" name="associated_upc" id="associated_upc" value="" />
                <input type="hidden" name="associated_product_id" id="associated_product_id" value="" />
            </div>
            <div id="assocUpcAutoComplete"></div>
            <div class="clear"></div>
        </div>
    </form>
</div><!-- end #addCompPopup -->