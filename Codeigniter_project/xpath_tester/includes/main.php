<?php 
error_reporting(0);//(E_ALL);


echo "<h1>$test_url</h1>";

$page_cache = 'page_cache/'.substr(preg_replace('/[?:\/.]/', '', $test_url),7,55).'.html';
if(file_exists($page_cache)){
    $page = file_get_contents($page_cache);
}
else{
    $curl = curl_init($test_url);
    curl_setopt_array($curl, array(
        //CURLOPT_ENCODING=>'gzip'
        CURLOPT_RETURNTRANSFER=>true
        ,CURLOPT_COOKIEJAR => '/tmp/'.md5($test_url).'cookie.txt'
        ,CURLOPT_COOKIEFILE => '/tmp/'.md5($test_url).'cookie.txt'
        ,CURLOPT_TIMEOUT => 20
        ,CURLOPT_PROXYAUTH=>CURLAUTH_ANY
        ,CURLOPT_HTTPHEADER=> array("Accept-Charset: utf-8;q=1,*;q=0.7")
        ,CURLOPT_FOLLOWLOCATION => true
        ,CURLOPT_SSL_VERIFYPEER => false
    ));
    $page = curl_exec($curl);
    /*check for execution errors*/
    if(curl_errno($curl)) {
        echo 'cUrl error: ' . curl_error($curl);
        exit;
    }
    curl_close($curl);
	
	// purify the HTML and cache it
	$config = array('show-errors' => 0);
	$page = tidy_repair_string($page, $config);
    file_put_contents($page_cache, $page);
}

$DOM = new DOMDocument;
if (!$DOM->loadHTML($page)){
	$errors="";
	foreach (libxml_get_errors() as $error)  {
		$errors.=$error->message."<br/>"; 
	}
	libxml_clear_errors();
	print "libxml errors:<br>$errors";
	return;
}

$xpath = new DOMXPath($DOM);
foreach ($arrXpaths as $name => $query){
	query_display($name, $query);
}

$hostname = parse_url($test_url, PHP_URL_HOST);
$hostname = str_replace(array('www.', '.com', '.net', '.org'), '', $hostname);

// save xpaths into TSV
@mkdir("xpaths/".$hostname);
$arrXpaths['Product URL'] = $test_url;
$xpathsFn = "xpaths/{$hostname}/{$hostname}.tsv";
$fhandle = fopen($xpathsFn, 'w');
fputcsv($fhandle, array_keys($arrXpaths), "\t");
fputcsv($fhandle, $arrXpaths, "\t");
fclose($fhandle);

//// create sample product file
//$productsFn = "products/{$hostname}.csv";
//if(!file_exists($productsFn)){
//    $arrProducts = array('Brand', 'Description', 'UPC', 'URL');
//    $fhandle = fopen($productsFn, 'w');
//    fputcsv($fhandle, $arrProducts, ",");
//    fclose($fhandle);
//}

/******************************************************************************/
function query_display($name, $query, $context_query=''){
	global $xpath;
	echo "<hr>";
	echo "<h2>$name</h2><p><b><code>$query</code></b> </p>";
	if(empty($query)) return '';
	$nodelist = $xpath->evaluate($query);
	
	echo "<PRE>";
	echo "Raw elements:\n";
    echo '<div style="background-color:	#FFF9F0;">';
	print_r(domNodeList2Array($nodelist));
	echo '</div>';
	echo "\nResult:\n";
    echo '<div style="background-color:HoneyDew;">';
	foreach ($nodelist as $node) {
		if(is_subclass_of($node,"DOMNode")){
                echo $node->nodeValue."<br>";
        }
        elseif( get_class($node) === "DOMNodeList"){
			$res= array();
			foreach($nodelist=$node as $node){
				$res[]=$node->nodeValue;
			}
			print_r($res);
		}
		else{
			echo $node;
		}
	}
    echo '</div>';
    echo "</PRE>";
}

function domNodeList2Array($items){
	foreach($items as $element) {
	  $xml = $element->ownerDocument->saveXML($element);
	  $arr[] =  htmlentities($xml);
	}
	return $arr;	
}
?>