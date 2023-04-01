<P>Here's an example:</P>
<?php
$test_url = 'http://www.myvillagegreen.com/jarrow-formulas-jarro-dophilius-eps.html#120';
$arrXpaths = array(
    'test' => '',
					'Price' => '//table[@id="super-product-table"]',
					'Retail Price' => '//table[@id="super-product-table"]/tbody/tr/td[2]',
					'Product Name' => '//title',
					'UPC' => '',
					'SKU' => '//form/font/text()[1]',
					'Image' => '',
				  );

require('includes/main.php');
?>
