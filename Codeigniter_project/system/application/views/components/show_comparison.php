<section class="clear select_report<?= ($display ? '' : ' hidden') . ($is_first ? ' search_first' : ''); ?>" id="show_comparison_container">
    <div class="leftCol">
        <label>Show Comparisons</label>
    </div>
    <div class="rightCol reports_radio">
        <input type="radio" name="show_comparison" id="show_comparison_1" value="1" <?php if ($show_comparison): ?> checked="checked" <?php endif; ?> /> Yes		
        <input type="radio" name="show_comparison" id="show_comparison_0" value="0" <?php if ($submitted AND !$show_comparison): ?> checked="checked" <?php endif; ?> /> No
    </div>
</section><!-- #show_comparison_container -->
