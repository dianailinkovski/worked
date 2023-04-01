<div class="content_dashboard">

    <?php if (isset($marketplaces[0]) || isset($retailers[0])): ?>
        <?php if (isset($marketplaces[0])): ?>

            <table width="100%" class="reportTable sortable table table-bordered table-striped table-success table-responsive">

                <thead>
                    <tr class="row_title">
                        <th width="40%">Marketplace</th>
                        <th width="20%">Products Found</th>
                        <th width="20%">Merchants</th>
                        <th width="20%">Last Tracking</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($marketplaces as $key => $data): ?>

                        <?php
                        
                        $class = ($key % 2 == 1) ? 'evenRow' : 'oddRow';
                        
                        $crawl_info = !empty($last_crawl[$data['marketplace']]) ? $last_crawl[$data['marketplace']] : FALSE;
                        $crawl_start = !empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : '';
                        
                        ?>

                        <tr class="<?php echo $class; ?>">
                            <td><a href="<?php echo base_url() . 'whois/report_marketplace/' . $data['marketplace'] ?>"><?php echo $data['display_name']; ?></a></td>
                            <td><?php echo number_format($data['total_products']); ?></td>
                            <td><?php echo number_format($data['total_listing']); ?></td>
                            <td><?php echo $crawl_start ?></td>
                        </tr>

                    <?php endforeach ?>

                </tbody>
            </table><br>

    <?php endif; ?>
    
    <?php if (isset($retailers[0])): ?>

        <table width="100%" class="reportTable sortable table table-bordered table-striped table-success table-responsive">

            <thead>
                <tr class="row_title">
                    <th width="40%">Retailer</th>
                    <th width="20%">Products Found</th>
                    <th width="20%">Violations</th>
                    <th width="20%">Last Tracking</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($retailers as $key => $data): ?>
        
                    <?php 
                    
                    $class = ($key % 2 == 1) ? 'evenRow' : 'oddRow';
                    $crawl_info = !empty($last_crawl[$data['marketplace']]) ? $last_crawl[$data['marketplace']] : FALSE;
                    $crawl_start = !empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : '';
                    $violation_count = !empty($market_violations[$data['marketplace']]) ? $market_violations[$data['marketplace']] : '';
                    
                    ?>
                    
                    <tr class="<?php echo $class; ?>">
                        <td><a href="<?php echo base_url() . 'whois/report_merchant/' . $data['marketplace'] . '/' . $data['id'] ?>"><?php echo $data['display_name']; ?></a></td>
                        <td><?php echo number_format($data['total_products']); ?></td>
                        <td><?php echo $violation_count; ?></td>
                        <td><?php echo $crawl_start ?></td>
                    </tr>
        
                <?php endforeach; ?>

            </tbody>
        </table>

    <?php endif; ?>
    
<?php else: ?>

    <p>
        No records found.
    </p>

<?php endif; ?>

</div>