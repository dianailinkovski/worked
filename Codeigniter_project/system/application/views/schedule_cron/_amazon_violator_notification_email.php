MAP Violation Warning - Email #<?= $email_level?>-<?= $email_repeat?>
Date: <?=date('m/d/Y', $product['timestamp']);?>
Time: <?=date('h:i A', $product['timestamp']);?>
Name: <?=$product['title'];?>
UPC: <?=$product['upc_code'];?>
Retail: $<?=number_format($product['retail_price'], 2);?>
MAP: <?php if (!empty($product['map']) && $product['map'] != 0 && $product['map'] != 0.00):?>$<?=number_format($product['map'], 2);?><?php endif;?>
Price: $<?=number_format($product['price_floor'], 2);?>
URL: <?=extractDomainByURL($product['url']);?>