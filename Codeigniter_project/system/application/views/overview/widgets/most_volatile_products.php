<div class="panel panel-default">
    <div class="panel-heading">
        Today's Largest Price Violations
    </div>
    <!--  
    
    <?php //var_dump($ranked_products); ?>
    <?php var_dump($product_trend_ids); ?>
    
    -->
    <div class="panel-body">
        <table class="table table-bordered table-striped table-success table-responsive" id="products-table">
            <thead>
                <tr>
                    <th>
                        Amount Under
                    </th>
                    <th>
                        Product
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; ?>
                <?php foreach ($ranked_products as $row): ?>
                    <tr>
                        <td>
                            $<?php echo number_format($row['price_diff'], 2); ?> 
                            <!-- upc: <?php echo $row['upc']; ?> -->
                            <!-- $trend_ids[$row['price_diff']]: <?php echo $trend_ids[$row['price_diff']]; ?> -->
                            <!-- $product_trend_ids[$row['upc']]: <?php echo $product_trend_ids[$row['upc']]; ?> -->
                        </td>
                        <td>
                            <a href="/violationoverview/violated_product/<?php echo $row['product']['id']; ?>"><?php echo $row['product']['title']; ?></a>
                        </td>
                    </tr>
                    <?php if ($rank == 5) break; ?>
                    <?php $rank++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>        
    </div>
</div>