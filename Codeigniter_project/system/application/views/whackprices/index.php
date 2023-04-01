
<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Whack Prices</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <style>
        #instructions, #instructions th, #instructions td {
            border: 1px solid black;
        }
        </style>
        
        <p>(*work in progress)</p>
        
        <p>These products were found during crawls in the last 24 hours, and their prices were less than 30% of MAP.<br/>
        Investigate and take action.</p>
        <table id="instructions" style="border: groove; width: 60%">
            <tr>
                <th style="background:#F6CECE">Problem</th>
                <th style="background:#CEF6D8">Suggested Solution</th>
            </tr>
            <tr>
                <td>Crawled price is accurate</td>
                <td>Do nothing</td>
            </tr>
            <tr>
                <td>Wrong product (Retailer)</td>
                <td>1. Login via GA website to StickyVision Catalog, and correct the Lookup URL<br/>
                    [2. Click to send to worker queue.]
                </td>
            </tr>
            <tr>
                <td>Wrong product (Marketplace)</td>
                <td>Alert programmer (Chris)</td>
            </tr>
            <tr>
                <td>UPC is right but there are several products on webpage and the crawler has picked the wrong one.</td>
                <td>Alert programmer (Chris)</td>
            </tr>
            <tr>
                <td>Something is weird / unexplainable</td>
                <td>Alert programmer (Chris)</td>
            </tr>
        <!--
            <tr>
                <td></td>
                <td></td>
            </tr>
        -->
        </table>
        </br>
            <table>
                <tr style="background:silver">
                    <th>Store</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>UPC</th>
                    <th>MAP</th>
                    <th>Price</th>
                    <th>Percent</th>
                    <th>URL</th>
                    <th>Action</th>
                </tr>
                <?php $i = 1;?>
                <?php foreach($products as $product):?>
                    <tr style="background:<?php echo $i%2 ? '#E6E6E6':'#FFFFFF'; $i++?>;">
                        <td><?=$product->store_name?></td>
                        <td><?=$product->title?></td>
                        <td><?=$product->sku?></td>
                        <td><?=$product->upc?></td>
                        <td><?=$product->ap?></td>
                        <td><?=$product->mpo?></td>
                        <td><?=intval($product->percent)?>%</td>
                        <td><a href="<?=$product->l?>" target="_blank">Open new window</a></td>
                        <td>...</td>
                    </tr>
                <?php endforeach; ?>
            </table>
        
        
        <?php
        /*
         *print_r($products); exit;
        products stdClass Object
        (
            [id] => 7357395
            [um] => nutritiongeeks#684088232333
            [dt] => 1431554138
            [ap] => 28.79
            [ar] => nutritiongeeks
            [il] => /images/53648.jpg
            [l] => http://www.nutritiongeeks.com/bio-active-silver-hydrosol-8/53648.html
            [mil] => 
            [mpo] => 7.20
            [msp] => 0.00
            [mu] => http://www.nutritiongeeks.com
            [t] => Bio Active Silver Hydrosol
            [pid] => 27035
            [upc] => 684088232333
            [mid] => 12488
            [ss] => 20150513/5cf8dc1a082c3f6389978280668246c7.png#OK
            [rp] => 35.99
            [wp] => 21.59
            [percent] => 25.008682
        )
        */
        ?>
      
    </div>        
</div>        