<?php if (count($violatedProducts) > 0)
{ ?>
    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable sortable">
        <thead>
            <tr>
                <th width="40%">Title</th>
                <th width="15%">UPC</th>
                <th width="15%">Retail</th>
                <th width="15%">Wholesale</th>
                <th width="15%">MAP</th>
            </tr>
        </thead>
        <tbody><?php foreach ($violatedProducts as $key => $data)
    { ?>
                <tr>
                    <td><a href="<?= base_url() . 'violationoverview/violated_product/' . $data['id']; ?>"><?= html_entity_decode($data['title']); ?></a></td>
                    <td><?= $data['upc_code']; ?></td>
                    <td><?= '$' . $data['retail_price']; ?></td>
                    <td><?= '$' . $data['wholesale_price']; ?></td>
                    <td><?= '$' . $data['price_floor']; ?></td>
                </tr><?php }
    ?>
        </tbody>
    </table><?php }
else
{
    ?>
    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable sortable">
        <tr><td align="center">No record found.</td></tr>
    </table><?php }
?>
