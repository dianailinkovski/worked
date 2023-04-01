<?php
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=9fe43fce-c53f-4abd-8666-afbbdfe18e5f&gas=Ohhira#60';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=7961ce91-bc1e-44dd-9e40-d9a84bd61a65#180';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=9fe43fce-c53f-4abd-8666-afbbdfe18e5f&gas=Ohhira#30';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=3870495e-15b3-4843-b8b2-257de4cbe869#60';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=20442b61-3d62-4c41-8022-1d12db3a6382#120';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=57464093-5236-45de-b824-a217b3443d83#30';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=01deb57c-ae78-4816-835a-1ee37d138300#2';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=806a0eb9-b619-463c-9420-4fb0cd281b9d#240';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=656d6e95-66f3-4ef5-99cc-d54023772161#60';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=eced3eb3-2911-4fd4-8edd-ab6bce237f67#30';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=5694f575-59ae-416e-937a-6edf2ced13c5#30';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=3870495e-15b3-4843-b8b2-257de4cbe869#120';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=db16c56f-a87a-4f17-a06d-5f9d81157d5d#90';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=806a0eb9-b619-463c-9420-4fb0cd281b9d#360';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=7961ce91-bc1e-44dd-9e40-d9a84bd61a65#90';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=886c096e-58cc-4e46-8be7-b1ee71fce82b#100';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=16d49740-0443-430f-8ca7-40fbebab961f#90';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=656d6e95-66f3-4ef5-99cc-d54023772161#30';
$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=a0e74d25-7dda-434d-89cf-7a5394797ed1';
//$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=7831f93b-5de7-4c50-97e7-fc947aebb58a#12';
//$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=eced3eb3-2911-4fd4-8edd-ab6bce237f67#60';
//$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=0c5963ee-d98d-4265-8f18-25e43a059c13#100';
//$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=9a15c8e0-0cf0-48f0-a90d-4bab31decc06#908';
//$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=c24ae8c3-7f2c-4287-9617-7c5e89aa55c8#540';
//$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=0637dcac-dbd3-4e35-9c8f-0f058d93a8ae#60';
//
//
//$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=b9cb87fa-40cd-4411-9b11-85bb29c48174#2';
//$test_url = 'http://www.valleynaturals.com/ct_detail.html?pgguid=ebea7416-4d8b-40ff-9e54-b45dbea842d3#504';

$arrXpaths = array(
                'offers' => "//table[@id='tableOptions']//tr",//td[1]/text()" ///td/span
                'product.name' => "//h1/text()",
                'product.price_listed' => "//td[@class='price']/text()[3]",
                'product.price_retail' => "",
                'product.image_url' => "",
                'product.sku' => "//td[1]/text()"
            );

require('includes/main.php');
?>
