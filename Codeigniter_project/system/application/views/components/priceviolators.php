<?php if (count($priceViolators) > 0)
{ ?>
    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable sortable">
        <thead>
            <tr>
                <th class="overviewtitleTh">Merchant</th>
                <th class="overviewtitleTh">Location</th>
                <th class="overviewtitleTh">Product Violations</th>
                <th class="overviewtitleTh">Notification Msg.</th>
                <th class="overviewtitleTh">Repeat Notifications</th>
                <th class="overviewtitleTh">Last Violation Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
			//sort
        	$names = array();
            foreach ($priceViolators as $key2 => $data2)
            {
            	if ( isset($data2['crowl_merchant']['merchant_name']) ) $names[] = ucfirst($data2['crowl_merchant']['merchant_name']); 
            	else $names[] = "";
            }
            array_multisort($names, SORT_ASC, $priceViolators);
            
            foreach ($priceViolators as $key => $data)
            {
                $data2 = $data['crowl_merchant'];
                if ( !isset($data2['merchant_name']) ) continue;
                
                $name = (!empty($data2['original_name']) && $data2['original_name'] != NULL) ? $data2['original_name'] : $data2['merchant_name'];
                ?>
                <tr>
                    <td><a href="<?= base_url() . 'violationoverview/violator_report/' . $data2['id']; ?>"><?php echo $name; ?></a></td>
                    <td><?php echo (isset($data['crowl_merchant']['merchant_url']) ? $data['crowl_merchant']['merchant_url']:"") ?></td>
                    <td><?php echo (isset($data['total_violations']) ? $data['total_violations']:"") ?></td>
                    <td><?php echo (isset($data['violation_status']) ? $data['violation_status']:"") ?></td>
                    <td><?php echo (isset($data['repeat_vioaltor']) ? $data['repeat_vioaltor']:"") ?></td>
                    <td><?php echo (isset($data['last_violator']) ? $data['last_violator']:"") ?></td>
                </tr><?php }
    ?>
        </tbody>
    </table><?php }
else
{
    ?>
    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable sortable">
        <tr><td align="center">No records found.</td></tr>
    </table><?php }
        ?>
