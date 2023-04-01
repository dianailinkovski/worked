<!-- start byproduct -->
<section class="clear select_report<?= ($display ? '' : ' hidden') . ($is_first ? ' search_first' : ''); ?>" id="product_container">
    <div class="leftCol">
        <label>Select Products</label>
    </div>
    <div class="rightCol">
        <?php if (empty($searchProducts))
        { ?>
            <div class="autoCompleteContainer clearAfter">
                <input type="text" class="product product_name" name="product_name[]" value="" />
                <input type="hidden" name="products[]" value="" />
            </div>
            <?php
        }
        else
        {
            $c = 0;
            $n = sizeof($searchProducts);
            foreach ($searchProducts as $pId => $pName):
                ?>
                <div class="autoCompleteContainer">
                    <input type="text" class="product product_name" name="product_name[]" value="<?= $pName; ?>" />
                    <input type="hidden" name="products[]" value="<?= $pId; ?>" />
        <?php if ($c !== 0): ?>
                        <span class="product-minus button jsLink clear">
                            <span class="buttonCornerL"></span>
                            <span class="buttonR">
                                <img src="<?= frontImageUrl() ?>icons/minus.png" alt="Remove" />Remove this Product
                            </span>
                        </span>
        <?php endif; ?>
                    <span class="product-plus button jsLink clear">
                        <span class="buttonCornerL"></span>
                        <span class="buttonR">
                            <img src="<?= frontImageUrl() ?>icons/16/13.png" alt="Add" />Add another Product
                        </span>
                    </span>
                </div>
        <?php
        $c++;
    endforeach;
}
?>
    </div>
</section>
<!-- end byproduct -->