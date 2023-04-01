
PROJECT
---------
connect to ecommerce platforms and extract prices based on upc

ENTITIES
----------
vendor: the supplier of product.
brand: the visible name of the vendor. ! 1 vendor can have multiple brands.
merchant: e-commerce platform. ID: root URL
seller: the merchant or individual seller account when the merchant is multi-seller platform. ID: canonical URL
product: a specific product reference. ID: UPC code. 

ARCHITECTURE
--------------
main.php 			command line test program
spider_<domain>_parser.php	parsing class for scraping http://www.<domain>/
spider_<domain>_controller.php	assembly class for scraping http://www.<domain>/
api_stickybusiness.php		internal api for accessing all spiders through a unified interface
api_productfinder.php		public api for searching product information through spiders.
test/				regression testing scripts and tests data
spider_lib_ag/			scraping library by lenzai.

INSTALL
---------
sudo apt-get install php5-cli php5-curl

TESTING
---------
1- regression testing.
$ cd test; ./test_acceptance.sh; diff tmp_acceptance data/ref_acceptance

2- commandline manual.
$ php main.php -h
