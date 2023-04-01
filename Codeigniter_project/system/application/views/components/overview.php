<?php
/**
 * overview_dashboard.php
 *
 * Current Status screen for price and violation overview
 */
$widget_class = ' fourWidgets';
if ($totalRetailers > 0):
    $widget_class = ' fiveWidgets';
endif;
?>

<div class="widget<?= $widget_class ?>">
    <h3>Total Merchants</h3>
    <div class="gaugeContainer">
        <div id="total_merchants_gauge"></div>
    </div>
</div>

<div class="widget<?= $widget_class ?>">
    <h3>Products Monitored</h3>
    <?php
    $mLink = base_url() . 'catalog/index/' . $this->store_id;
    $products_monitored = (String) $products_monitored;
    $pmChars = strlen($products_monitored);
    $numberCount = $pmChars > 3 ? ' highCount' : '';
    ?>
    <div class="tile_container<?php echo $numberCount; ?>"><?php if ($mLink): ?><a href="<?= $mLink ?>"><?php
            endif;
            for ($i = 0; $i < $pmChars; $i++):
                ?>
                <span class="mech_number_tile" id="mech<?= $products_monitored[$i] ?>"></span><?php
            endfor;
            if ($mLink):
                ?></a><?php endif; ?>
    </div>
</div>

<div class="widget<?= $widget_class ?>">
    <h3>Markets With Violations</h3>
    <?php
    $i = 0;
    $num_markets = count($violatedMarketplaces);
    if ($num_markets > 0):
        foreach ($violatedMarketplaces as $key => $value):
            $i++;
            $violation_count = isset($market_violations[$key]) ? $market_violations[$key] : 0;
            if ($violation_count > 0):
                ?>
                <a href="<?= base_url() . 'violationoverview/report_marketplace/' . strtolower($key) ?>" class="linkRow clear<?= $i == $num_markets ? ' last' : '' ?>">
                    <?php $url = frontImageUrl()."/market_logos/{$key}.png";?>
                    <?php if(is_url_exist($url)):?>
                        <img src="<?= frontImageUrl() ?>market_logos/<?= $key ?>.png" alt="<?= marketplace_display_name($key) ?>">
                    <?php else:?>
                        <div style="float:left"><?= marketplace_display_name($key) ?></div>
                    <?php endif;?>
                    <span class="violationCount"><?= $violation_count ?></span>
                </a>
                <?php
            endif;
        endforeach;
    endif;
    ?>
</div>

<?php if ($totalRetailers > 0): ?>
    <div class="widget<?= $widget_class ?>">
        <h3>Retailers With Violations</h3>
        <div class="violations_container" id="retailer-violation-list">
            <?php
            $i = 0;
            $num_retailers = count($violatedRetailers);
            if ($num_retailers > 0):
                foreach ($violatedRetailers as $key => $value):
                    $i++;
                    $violation_count = isset($market_violations[$key]) ? $market_violations[$key] : 0;
                    if ($violation_count > 0):
                        ?>
                        <a href="<?= base_url() . 'violationoverview/report_marketplace/' . strtolower($key) ?>" class="linkRow clear<?= $i == $num_retailers ? ' last' : '' ?>">
                        <?php $url = frontImageUrl()."/market_logos/{$key}.png";?>
                            <?php if(is_url_exist($url)):?>
                                <img src="<?= frontImageUrl() ?>market_logos/<?= $key ?>.png" alt="<?= marketplace_display_name($key) ?>">
                            <?php else:?>
                                <div style="float:left"><?= marketplace_display_name($key) ?></div>
                            <?php endif;?>
                            <span class="violationCount"><?= $violation_count ?></span>
                        </a>
                        <?php
                    endif;
                endforeach;
            endif;
            ?>
        </div>
        <p>
            <a class="show-more" id="retailer-violation-list-show-more" href="#">Show More</a>
        </p>
    </div>
<?php endif; ?>

<div class="widget last<?= $widget_class ?>">
    <h3>Products In Violation</h3>
    <div id="repChartContainer"><a href="<?= base_url() ?>violationoverview"></a></div>
</div>

<script type="text/javascript">
              
$(document).ready(function() {
	
    $('#retailer-violation-list-show-more').click(function(){
        $('#retailer-violation-list').toggleClass('retailer-violation-list-big');
        $('#retailer-violation-list-show-more').html($('#retailer-violation-list-show-more').text() == 'Show More' ? 'Show Less' : 'Show More');
        return false;
    });
    
});

</script>
