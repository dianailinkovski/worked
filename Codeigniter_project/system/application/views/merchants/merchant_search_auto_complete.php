<h4>
    Link to Existing Merchant?
</h4>
<?php if (empty($merchants)): ?>
    <p>
        No merchants were found with the given name. Or merchants that were found are already linked to this merchant.
    </p>
<?php else: ?>
    <table class="table table-bordered table-striped table-success table-responsive" id="merchant-auto-complete">
        <thead>
            <tr>
        	      <th align="left">Select</th>  
        		    <th align="left">Merchant</th>
        		    <th align="left">Website</th>
        		    <th align="left">Marketplace</th>
        		    <th align="left">Products Selling</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($merchants as $merchant): ?>
                <?php if (isset($merchant['merchant_name'])): ?>    
                    <tr>
                        <td>
                            <input class="merchant-select" type="radio" value="<?php echo $merchant['id']; ?>" name="existing_merchant_id" class="radio">
                        </td>
                        <td>
                            <?php echo $merchant['merchant_name']; ?>
                            <!-- <?php echo $merchant['id']; ?> -->                    
                        </td>  
                        <td>
                            <?php if ($merchant['original_name'] == $merchant['marketplace'] || $merchant['seller_id'] == $merchant['marketplace']): ?>
                                <a href="<?php echo $merchant['merchant_url']; ?>" target="_blank"><?php echo $merchant['merchant_url']; ?></a>
                            <?php else: ?>
                                <a href="<?php echo $merchant['marketplace_url']; ?>" target="_blank"><?php echo ucfirst($merchant['marketplace']); ?> Seller Page</a>
                            <?php endif; ?>                         
                        </td>  
                        <td>
                            <?php if ($merchant['original_name'] != $merchant['marketplace']): ?>
                                <?php echo ucfirst($merchant['marketplace']); ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>                        
                        </td>
                        <td>
                            <?php echo $merchant['product_count']; ?>
                        </td>
                    </tr>    
                <?php endif; ?>     
            <?php endforeach; ?>
        </tbody>
    </table>            
<?php endif; ?>

<script type="text/javascript">
      
$(document).ready(function() {

    $('.merchant-select').click(function() {
        $('#new-merchant-details').hide();
    });
	
});

</script>