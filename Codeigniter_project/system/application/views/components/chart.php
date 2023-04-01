<div class="graph_cont" id="repChartContainer"></div>
<div class="graphKeys">
    <?php
    $productArray = array();
    if (isset($my) && $my == 'whois' && isset($marketplace_keys)):
        if (count($marketplace_keys) > 0):
            $marektPlaceListCount = count($marketplace_keys);
            foreach ($marketplace_keys as $key => $val):
                ?>
                <span style="display:none">
                    <input type="checkbox" name="series[]" checked="checked" id="series_check_box_<?= $key ?>" value="<?= $key ?>" onclick="hideShowSeries(this, <?= $marektPlaceListCount ?>)" class="mt27 mr5">
                </span>
                <span class="mt27 mr5 bar-gar-options" style="display: inline-block; margin-left: 2px;">
                    <span class="squareKey" style="background-color:#<?= marketplace_graph_color($val) ?>"></span>
                    <a href="javascript:;" class="chart_filter" rel="<?= $key ?>"><?= marketplace_display_name($val) ?></a>
                </span><br /><?php
            endforeach;
        endif;
    else:
        
    
        $search_product_ids = $this->input->post("products");
        $search_product_names = $this->input->post("product_name");
    
        if ( !empty($search_product_ids) ) {
            if ( !is_array($search_product_ids) ) $search_product_ids = array($search_product_ids);
            if ( !is_array($search_product_names) ) $search_product_names = array($search_product_names);
            
            for ( $i = 0; $i < count($search_product_ids); $i ++ ) {
                if ( isset($gData['data']['result'][$search_product_ids[$i]]) ) continue;
            ?>    
                <div class="product_save">
                    <span class="squareKey" style="background-color:#000"></span>
                    <span class="draw_lines">
                        <b><?= $search_product_names[$i] ?></b>: There is no search result<br><br>
                    </span>
                </div>
            <?php
            }
        }
        
        $i = 0;
        foreach ($gData['data']['result'] as $prodId => $series):
            $has_outliers = (
                    $gData['type'] === 'scatter' AND
                    isset($gData['data']['columns'][$prodId]['stats']['outliers']) AND
                    count($gData['data']['columns'][$prodId]['stats']['outliers'])
                    );
            ?>
            <div class="product_save">
                <span class="squareKey" style="background-color:<?= $gData['data']['columns'][$prodId]['color']['hex'] ?>"></span>
                <span class="draw_lines">
					Show our price-points:<br>
                    <!--<b><?= getProductsTitle($prodId) ?></b><br>-->
                    <input type="checkbox" value="1" onclick="drawLines(<?= $prodId ?>, 'retail', this);" id="retail_<?= $prodId ?>" name="retail_<?= $prodId ?>"><label for="retail_<?= $prodId ?>">Retail</label>
                    <input type="checkbox" value="1" onclick="drawLines(<?= $prodId ?>, 'map', this);" id="map_<?= $prodId ?>" name="map_<?= $prodId ?>"><label for="map_<?= $prodId ?>">MAP</label>
                    <input type="checkbox" value="1" onclick="drawLines(<?= $prodId ?>, 'wholesale', this);" id="wholesale_<?= $prodId ?>" name="wholesale_<?= $prodId ?>"><label for="wholesale_<?= $prodId ?>">Wholesale</label>
                    <div class="clear"></div><?php if ($has_outliers): ?>
                        <input type="checkbox" value="1" onclick="removeOutliers(<?= $prodId ?>, this);" id="outliers_<?= $prodId ?>" name=outliers_<?= $prodId ?>"><label for="outliers_<?= $prodId ?>">Hide Outliers</label><?php endif;
            ?>
                </span>
            </div><?php
            $i++;
        endforeach;
    endif;
    ?>
</div>