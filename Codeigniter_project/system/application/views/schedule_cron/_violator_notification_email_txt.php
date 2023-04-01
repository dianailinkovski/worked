<?php if ( ! empty($message)): ?>
<?=$message?>
<?php endif; ?>

Violation Report:

<?php foreach ($productData as $product): ?>
----------------------------------------------------------------------
Marketplace: <?=ucfirst($product['marketplace']);?>

Date:        <?=date('m/d/Y', $product['timestamp']);?>

Time:        <?=date('h:i A', $product['timestamp']);?>

Name:         <?=$product['title'];?>

UPC:         <?=$product['upc_code'];?>

Retail:      $<?=number_format($product['retail'], 2);?>

MAP:         <?php if (!empty($product['map']) && $product['map'] != 0 && $product['map'] != 0.00):?>$<?=number_format($product['map'], 2);?><?php endif;?>

Price:       $<?=number_format($product['price'], 2);?>

URL:         <?=$product['url'];?>


<?php endforeach; ?>
