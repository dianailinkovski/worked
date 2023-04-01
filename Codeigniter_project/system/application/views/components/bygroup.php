<!-- start bygroup -->
<section class="clear select_report<?= ($display ? '' : ' hidden') . ($is_first ? ' search_first' : ''); ?>" id="product_container">
    <div class="leftCol">
        <label>Product Group</label>
    </div>
    <div class="rightCol">
        <div class="selectMenu">
            <div class="selectMenuToggle">SELECT GROUP</div>
            <div class="selectMenuDropdown">
                <?php
                if (!empty($product_groups)) :
                    foreach ($product_groups as $group) :
                        $checked = $group->id === $group_id ? ' checked="checked"' : '';
                        ?>
                        <div class="inputContainer">
                            <input type="radio" name="group_id" value="<?= $group->id ?>"<?= $checked ?> /><label for="<?= $group->id ?>"><?= $group->name ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
</section>
<!-- end bygroup -->
