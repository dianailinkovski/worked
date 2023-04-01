<?php
//hot fix -- nathan
if (empty($brands))
    $brands = array();
?>	

<?php if ($this->success_msg != ''): ?>
	<div class="success-msg">
		<?php echo $this->success_msg; ?>
	</div>
<?php endif; ?>

<?php if ($this->error_msg != ''): ?>
	<div class="error-msg">
		<?php echo $this->error_msg; ?>
	</div>
<?php endif; ?>

<!-- start overview_header -->
<div class="headlineArea clear">
    <!--
                        <div class="leftCol"><img src="/images/icons/product-icon.jpg" alt="Product Icon" width="34" height="37"></div>
    -->
    <div class="rightCol">
        <h2><?= $report_name; ?></h2>
        <div class="customSelect clearAfter">
            <?php $disable = (count($brands) == 1) ? 'disabled="disabled" class="hidden"' : ''; ?>
            <form action="<?= site_url('account/switch_brand') ?>" method="post" id="switchBrandForm">
                <select id="switchBrand" name="switchBrand"<?= $disable; ?>>
                    <!-- <option label="All Products" value="all">All Products</option> --><!-- Add does not support this yet. [Christophe] -->
                    <?php for ($i = 0, $n = count($brands); $i < $n; $i++): ?>
                        <option label="<?= $brands[$i]['store_name'] ?>" value="<?= $brands[$i]['store_id'] ?>" <?= $brands[$i]['store_id'] == $store_id ? ' selected="selected"' : '' ?>><?= $brands[$i]['store_name'] ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>
        <div>
        <? if($this->router->class == 'catalog'):?>
            <a href="<?= site_url('category/store');?>/storeId=<?= $store_id;?>">Categorize this catalog</a>
            <br>
            This catalog is mapped to these categories:
            <? if(empty($STORE_CATEGORIES)):?>
                {none selected yet}
            <? else:?>
                <? foreach($STORE_CATEGORIES as $sc):?>
                    <?= $sc['name']?>, 
                <? endforeach;?>
            <? endif;?>
        <? endif;?>
        </div>
        <p><a href="#" id="bookmarkPage" class="grayLink"><img src="/images/icons/16/80.png" width="16" height="16" class="linkIcon">Bookmark this page</a></p>
    </div>
</div>
<div id="bookmarkDialog" class="modalWindow dialog">
    <form id="bookmarkForm">
        <h1 align="center">Add Bookmark</h1>
        <div id="bookmarkMessage"></div>
        <div class="row_dat">
            <label for="bookmarkName">Shortcut Name:</label>
            <input type="text" name="bookmarkName" id="bookmarkName" value="" />
        </div>
        <div class="lbl">
            <input type="submit" id="bookmarkSubmit" value="Save" />
        </div>
    </form>
</div>
<!-- end overview_header -->
