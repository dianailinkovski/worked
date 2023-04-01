<h3>
    Do Not Sell List Report - <?php echo date('M j, Y'); ?>
</h3>

<p>
    Please find a report below of all merchants on your Do Not Sell list. To view your Do Not Sell list, 
    please visit: <a href="https://app.trackstreet.com/enforcement/do_not_sell">https://app.trackstreet.com/enforcement/do_not_sell</a>
</p>

<?php if (!empty($dns_merchants)): ?>
    <table style="margin-top: 20px; border-top: 1px solid #ccc;">
        <thead>
            <tr>
                <th style="background-color: #f1f1f1; padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">Merchant Name</th>
                <th style="background-color: #f1f1f1; padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">Website</th>
                <th style="background-color: #f1f1f1; padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">Marketplace</th>
                <th style="background-color: #f1f1f1; padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">DNS Start</th>
                <th style="background-color: #f1f1f1; padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">DNS Removal</th>
                <th style="background-color: #f1f1f1; padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">Times on List</th>
                <th style="background-color: #f1f1f1; padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dns_merchants as $merchant): ?>
                <tr>
                    <td style="padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">
                        <a href="https://app.trackstreet.com/merchants/profile/<?php echo $merchant['id']; ?>"><?php echo $merchant['profile_name']; ?></a>
                    </td>
                    <td style="padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">
                        <?php if ($merchant['original_name'] == $merchant['marketplace'] || $merchant['seller_id'] == $merchant['marketplace']): ?>
                            <a href="<?php echo $merchant['merchant_url']; ?>" target="_blank"><?php echo $merchant['merchant_url']; ?></a>
                        <?php else: ?>
                            <a href="<?php echo $merchant['marketplace_url']; ?>" target="_blank"><?php echo ucfirst($merchant['marketplace']); ?> Seller Page</a>
                        <?php endif; ?>                        
                    </td>
                    <td style="padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">
                        <?php if ($merchant['original_name'] != $merchant['marketplace']): ?>
                            <?php echo ucfirst($merchant['marketplace']); ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td> 
                    <td style="padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">
                        <?php echo $merchant['start_date']; ?>
                    </td>
                    <td style="padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">
                        <?php echo $merchant['removal_date']; ?>
                    </td>  
                    <td style="padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">
                        <?php echo $merchant['num_of_times']; ?>
                    </td> 
                    <td style="padding: 6px 4px 6px 4px; border-bottom: 1px solid #ccc;">
                        <?php if (intval($merchant['is_permanent']) == 1): ?>
                            Permanent
                        <?php else: ?>
                            Temporary
                        <?php endif; ?>
                    </td>                                                                                                                   
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>
        There are currently no merchants on your Do Not Sell list.
    </p>    
<?php endif;?>    