<?php
$solidLineStyle = 'style="background-color:#FBB925;height:1px;line-height:1px;overflow:hidden;"';
?>
<body>
    <div style="color:#666;font-family:Arial,Helvetica,sans-serif;min-height:400px;">
        <table border="0" cellpadding="0" cellspacing="0" width="580">
            <tr>
                <td width="515" align="left"><img src="<?= $this->config->item('public_base_url') . 'images/nav/sticky-vision.png'; ?>" alt="Logo" /><?php if (isset($merchant_logo) && !empty($merchant_logo)): ?> <img src="http://<?= $this->config->item('s3_bucket_name') . '/stickyvision/brand_logos/' . $merchant_logo; ?>" /><?php endif; ?></td>
                <td width="65" align="right">
                    <span style="font-size:23px;font-family:'Times New Roman', Times, serif;color:#6a696e;">REPORTING</span><br />
                    <span style="font-size:9px;color:#6a696e;font-family:Arial, Helvetica, sans-serif;font-weight:bold;"><?php if (isset($headerDate)) echo 'Dates: ' . $headerDate; ?></span>
                </td>
            </tr>
            <tr>
                <td colspan="2" <?= $solidLineStyle; ?>>&nbsp;</td>
            </tr>
            <tr>
                <td height="10" colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" style="color:#fff;padding:5px;font-size:12px;font-weight:bold;" bgcolor="#00a0d1"><?php
                    if (isset($report_type) && $report_type == 'pricingviolation')
                        $title = str_replace(' pricing activity ', ' Price Violations ', $title);
                    echo $title;
                    ?>
                </td>
            </tr><?php if (!empty($graph_image_name)): ?>
                <tr>
                    <td colspan="2" style=" padding:10px 0 0"><?= empty($graph_image_name) ? '' : '<img src="http://' . $this->config->item('s3_bucket_name') . '/stickyvision/graph_images/' . $this->store_id . '/' . $graph_image_name . '" width="760" />'; ?></td>
                </tr><?php endif;
                    ?>
        </table>

        <?= $content ?>

        <table border="0" cellpadding="0" cellspacing="0" width="580">
            <tr>
                <td height="10"></td>
            </tr>
            <tr>
                <td width="580" <?= $solidLineStyle; ?>>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><?= '&copy;' . date('Y') . ' Sticky Business, LLC - www.juststicky.com'; ?></td>
            </tr>
        </table>
    </div>
</body>
</html>
