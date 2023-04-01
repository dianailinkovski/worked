<div class="panel panel-default">
    <div class="panel-heading">
        Today's Most Violated Products
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-striped table-success table-responsive" id="products-table">
            <thead>
                <tr>
                    <!-- 
                    <th>
                        Rank
                    </th>
                    -->
                    <th>
                        Product
                    </th>
                    <th>
                        Violations
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; ?>
                <?php foreach ($ranked_products as $product): ?>
                    <tr>
                        <!--  
                        <td>
                            <?php echo $rank; ?>
                        </td>
                        -->
                        <td>
                            <a href="/violationoverview/violated_product/<?php echo $product['id']; ?>"><?php echo $product['title']; ?></a>
                        </td>
                        <td>
                            <?php echo $product['violaton_count']; ?>
                        </td>
                    </tr>
                    <?php if ($rank == 5) break; ?>
                    <?php $rank++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>        
    </div>
</div>