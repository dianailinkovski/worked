<style>
    #crawlers th{
        background:silver;
        font-size: small;
    }
</style>
<h2>Crawlers Status</h2>

<p></p>

</br>
    <table id="crawlers">
        <tr>
            <th>Name</th>
            <th>Brand Page</th>
            <th>Retailer</th>
            <th>UPC Lookup</th>
            <th>SKU Lookup</th>
            <th>Frequency</th>
            <th>Active</th>
            <th>Product XPaths</th>
            <th>Brand XPaths</th>
            <th>Brand Detail XPaths</th>
            <th>Action</th>
        </tr>
        <? $i = 1;?>
        <? foreach($marketplaces as $marketplace):
                $marketplace = (object)$marketplace;
        ?>
            <tr style="background:<?php echo $i%2 ? '#E6E6E6':'#FFFFFF'; $i++?>;">
                <td><?=$marketplace->display_name?></td>
                <td><? if(!empty($marketplace->brands_url)):?>
                        <a href="<?=$marketplace->brands_url?>">Link</a>
                    <? endif;?>
                </td>
                <td><?=$marketplace->is_retailer?></td>
                <td><?=$marketplace->upc_lookup?></td>
                <td><?=$marketplace->upc_and_sku_search?></td>
                <td><?=$marketplace->crawl_frequency?></td>
                <td><?=$marketplace->is_active?></td>
                <td>...</td>
                <td>...</td>
                <td>...</td>
                <td>...</td>
            </tr>
        <? endforeach;?>
    </table>

<?php
/*
<pre>
print_r($marketplaces); exit;
marketplaces stdClass Object
(
    [id] => 799
    [name] => zazzle
    [display_name] => Zazzle.com
    [is_retailer] => 1
    [is_active] => 1
    [upc_lookup] => 0
    [brands_url] => 
    [upc_and_sku_search] => 0
    [crawl_frequency] => thrice
)
*/
?>