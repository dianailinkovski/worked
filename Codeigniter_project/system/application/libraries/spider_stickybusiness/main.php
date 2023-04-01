<?php

require_once 'api_stickybusiness.php';
require_once 'api_productfinder.php';
main();

function fatal_error($message) {
    $help = 'searching ecommerce by UPC and checking prices
	-t <url>	target website or target product url
	-i <filename>	input file to extract UPC code from
	-k <keyword>	search keyword for product extraction
	-v 0-2		verbosity level
	-x <action>
		product-search		~ search products with keyword
		product-details		~ extract details from product url
		product-search-upc 	~ search products from a list of upc
		fetch				~ download target url. (testing and debugging purpose)
	-s <seconds>	throttle http query for several seconds
	-p <proxy>		configure proxy [deprecated in favor of Proxy_ips_m class]
	
Example:
	php main.php  -t "http://www.livamed.com/" -x product-search-upc -i test/data/upc_acceptance.lst.csv
	php main.php  -x product-details -t "http://www.vitacost.com/airborne-effervescent-health-formula-orange"
	php main.php  -t "http://www.livamed.com/" -x product-search -k testing
	php main.php -t "http://www.google.com/shopping" -x product-search-upc -i upc.lst  -p 69.162.164.95:45623 -s 10';
    die("\n*** $message ***\n\n$help\n");
}

function main() {
    $opt = getopt('x:t:i:k:hv:p:');

    if (isset($opt['h']))
        fatal_error($help);

    if (isset($opt['v']))
        XPathHelper::$_debug = intval($opt['v']);

    if (isset($opt['p']))
        XPathHelper::$_proxy = $opt['p'];

    if (isset($opt['t']))
        $target = $opt['t'];
    else
        $target = null;

    if (!isset($opt['x']))
        fatal_error("-x argument is mandatory");

    if (isset($opt['s']))
        XPathHelper::$_sleep = (int) $opt['s'];

    if (isset($opt['i'])) {
        $input = explode("\n", file_get_contents($opt['i']));
        if (($tail = array_pop($input)) !== '')
            array_push($input, $tail);
    }
    else
        $input = array($target);

    if (isset($opt['k']))
        $keyword = $opt['k'];
    else
        $keyword = '';


    switch ($opt['x']) {
        case 'product-search':
            productSearch($keyword, $target);
            break;
        case 'product-details':
            foreach ($input as $url)
                productDetails($keyword, $url);
            break;
        case 'product-details2':
            foreach ($input as $url)
                productDetails2($url);
            break;
        case 'product-parse':
            foreach ($input as $url)
                productParse($url);
            break;
        case 'product-listall':
            productfinder_listAll($target);
            break;
        case 'product-search-upc':
            if (isset($opt['k']))
                priceCheck(array($keyword), $target);
            else
                priceCheck($input, $target);
            break;
        case 'product-search-upc2':
            if (isset($opt['k']))
                priceCheck2(array($keyword), $target);
            else
                priceCheck2($input, $target);
            break;
        case 'product-search-mpn':
            if (isset($opt['k']))
                fatal_error("unexpected -k option for action 'product-search-mpn'");
            else
                priceCheckMPN($input, $target);
            break;
        case 'fetch':
            XPathHelper::$_curlopts[CURLOPT_FOLLOWLOCATION] = true;
            $xph = new XPathHelper($target);
            echo $xph->dump();
            break;
        default:
            fatal_error("unexpected -x action: " . $opt['x']);
    }
}

/* * ***************************************************************************
 * Command handlers
 * 
 * ****************************************************************************
 */

function priceCheck($upcs, $target) {
    $spider = new StickyBusiness();
    foreach ($upcs as $upc) {
        if ($upc === '')
            continue;
        try {
            print_records(
                    $spider->searchUpc($upc, $target), explode('|', 'timestamp|product.price_listed|product.price_retail|product.url|product.name|merchant.url|product.sellers'), //'|product.sku|product.image_url|seller.name|seller.url');, $productFinderFields ,
                    array('upc' => $upc)
            );
        } catch (StickyBusinessException $e) {
            echo $upc, "\t", $e->getMessage(), "\n";
        }
    }
}

function priceCheck2($upcs, $target) {
    $pf = new ProductFinder();
    foreach ($upcs as $upc) {
        if ($upc === '')
            continue;
        try {
            print_records(
                    $pf->search(new ProductFinderRequest($upc), $target), explode('|', 'timestamp|product_sku|product_name|product_price_listed|product_url|merchant_url|product_image_url|seller_name|seller_url|product_price_retail'), array('upc' => $upc)
            );
        } catch (ProductFinderException $e) {
            echo "$upc\t$target\t" . $e->getMessage() . "\n";
        }
    }
}

function priceCheckMPN($requests, $target) {
    $pf = new ProductFinder();
    $header = str_getcsv(array_shift($requests));
    foreach ($requests as $request) {
        $request = array_combine($header, str_getcsv($request));
        if ($request === '')
            continue;
        try {
            $results = $pf->search(new ProductFinderRequest(null, $request['Product Name'], $request['Brand'], $request['MPN']), $target);
            print_records(
                    $results, explode('|', 'timestamp|product_sku|product_name|product_price_listed|product_url|merchant_url|product_image_url|seller_name|seller_url|product_price_retail'), $request
            );
        } catch (ProductFinderException $e) {
            echo var_export($request, true) . "\t$target\t" . $e->getMessage() . "\n";
        }
    }
}

function productfinder_listAll($target) {
    $pf = new ProductFinder();
    print_records(
            $pf->search(new ProductFinderRequest('12543'), $target), explode('|', 'timestamp|product_sku|product_name|product_price_listed|product_url|merchant_url|product_image_url|seller_name|seller_url|product_price_retail'), array('upc' => $upc)
    );
}

function productDetails($keyword, $target) {
    var_export($target);
    $spider = new StickyBusiness();
    if ($keyword === null or $keyword === '')
        var_export($spider->getProductDetails($target));
    else
        foreach ($spider->search($keyword, $target) as $record)
            print_records(array($spider->getProductDetails($record['product.url'])), null, array('keyword' => $keyword)
            );
}

function productDetails2($url) {
    if ($url === '')
        return;
    XPathHelper::$_curlopts[CURLOPT_FOLLOWLOCATION] = true; //FIXME: temporary until direct reference to amazon is replaced with api
    $spider = new Spider_AmazonCom_Controller();
    $xph = new XPathHelper($url);
//	$parser= new Spider_AmazonCom_Parser();
//	var_export($parser->parseProductDetails($xph));

    $product = $spider->getProductDetails($xph);
    $sellers = reset($product['product.sellers']);

    print_records(
            array($product), null, array("product.url" => $url, "sellers.url" => $sellers['sellers.url'])
    );
}

function productParse($url) {
    if ($url === '')
        return;
    echo "$url\t";
    $spider = new Spider_AmazonCom_Parser();
    $xph = new XPathHelper($url);

    var_export($spider->parseProductDetails($xph));
}

function productSearch($keyword, $target) {
    $spider = new StickyBusiness();
    print_records(
            $spider->search($keyword, $target), null, array("keyword" => $keyword)
    );
}

/* * ***************************************************************************
 *  Tools
 * ****************************************************************************
 */

/**
 * print array or object as tab delimited records
 * automatically print header on first line or when header changes
 * 
 * WARNING: when $fields_filter is null , build list of fileds from first record. 
 * 
 * @param $records an array of object or hasttables.
 * @param $fields_filter array of string with ordered name of fields to be printed.
 * @param $record_init	some additional data fields to be printed on each row on the left side.
 */
function print_records($records, $fields_filter, $record_init) {
    if (count($records) === 0)
        return;

    static $header = null;
    if ($fields_filter === null)
        if (is_array(reset($records)))
            $fields_filter = array_keys(reset($records));
        elseif (is_object(reset($record))) {
            $fields_filter = array_keys(get_object_vars(reset($records)));
            echo "retrieving object fields... " . var_export($fields_filter);
        }
        else
            throw new Exception(__FUNCTION__ . ": unexpected record type " . gettype(reset($records)));


    if ($header !== array_merge(array_keys($record_init), $fields_filter)) {
        $header = array_merge(array_keys($record_init), $fields_filter);
        echo implode("\t", $header) . "\n";
    }

    foreach ($records as $pr) {
        $data = $record_init;
        foreach ($fields_filter as $field) {
            if (is_array($pr))
                $data[$field] = isset($pr[$field]) ? $pr[$field] : "NULL";
            elseif (is_object($pr))
                $data[$field] = isset($pr->$field) ? $pr->$field : "NULL";
            else
                throw new Exception(__FUNCTION__ . ": unexpected record type " . gettype($pr));
        }
        foreach ($data as &$field)
            if (is_array($field))
                $field = "array[" . count($field) . "]";
            else
                $field = formatCSV($field);
        echo implode("\t", $data) . "\n";
    }
}

function formatCSV($field) {
    $field = str_replace('"', '""', $field);
    $field = str_replace("\n", '\n', $field);
    if (strpos($field, " ") !== false)
        $field = '"' . $field . '"';
    return $field;
}

?>
