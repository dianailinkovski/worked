<?php
// input csv file of Threshold products catalog
// run sql on live db
// * Author: Chris Fortune, http://cfortune.kics.bc.ca/

/**************************************************************************************************/
//$db_production_hostname = "ec2-50-16-62-159.compute-1.amazonaws.com";
$db_production_hostname = "ec2-54-225-181-31.compute-1.amazonaws.com";
$db_production_username = "test_mv2";
$db_production_password = "JbfjPq5q0iOuem";
$db_production_database = "test_mv2";
$link = mysql_connect($db_production_hostname, $db_production_username, $db_production_password) or die(mysql_error());
mysql_selectdb($db_production_database, $link);
/**************************************************************************************************/
$db_account_hostname = 'localhost';
//$db_account_hostname = '107.22.255.187';
$db_account_username = 'sticky_accounts';
$db_account_password = 'zxv^Mj3u7mGw';
$db_account_database = 'sticky_accounts';
$link2 = mysql_connect($db_account_hostname, $db_account_username, $db_account_password) or die(mysql_error());
mysql_selectdb($db_account_database, $link2);
/**************************************************************************************************/

$sampleUrls = $cache = $arrSql = array();

$filename = 'Threshold_price_catalog 11-5-14 csvREADY FOR IMPORT.csv';
echo "$filename\n";
$arrData = parse_csv_file($filename);
//print_r($arrData[0]); exit;
foreach($arrData as $row){
    $description = ''; //$row['Description'];
    $upc = ''; //$row['UPC'];
    $brand = ''; //$row['Brand'];
    $sku = ''; //$row['SKU'];
    foreach($row as $k=>$v){
        $k = ucfirst($k);
        
        //echo "$k=>$v\n";
        if($k == 'Final Desc'){ //Verbose_description
            $description = str_replace('<95>', '-', $v);
            //$description = ucwords(strtolower($description));
            continue;
        }
        /*
        elseif($k=='Wholesale_price'){
            $wholesale_price = $v;
            continue;
        }
        elseif($k == 'Size'){
            $size = $v;
            continue;
        }
        elseif($k == 'Retail_price'){
            $retail_price = $v;
            continue;
        }
        elseif($k == 'Skucode'){
            $sku = $v;
        }
        elseif($k == 'Brand'){
            $brand = ucwords(strtolower($v));
            continue;
        }
        */
        elseif($k == 'Upc'){
            $upc = $v;
            continue;
        }
        else{
            continue;
        }
    }
    if(empty($description) or empty($upc) ){ //or or empty($brand) or empty($sku) empty($wholesale_price) or empty($retail_price) or empty($size) ){
        echo "missing data\n"; //: description: $description, UPC: $upc, brand: $brand, SKU: $sku, Wholesale_price: $wholesale_price, Retail_price: $retail_price, Size: $size\n";
        print_r($row);
        //exit;
        continue;
    }
    
    // fix wrong description field
    update_product($upc, $description);
    echo "$upc, ";
    continue;

    $guid = set_global_user($brand);
    $ret = set_global_user_apps($guid);
    $uid = set_production_user($brand, $guid);
    $sid = set_store($brand, $uid);
    set_users_store($sid, $uid);
    set_brand_columns($sid, $uid);
    $pid = set_product($upc, $sid, $description, $brand, $upc, $sku, $wholesale_price, $retail_price);
    
    set_products_pricing($pid, 'wholesale_price', $wholesale_price);
    set_products_pricing($pid, 'retail_price', $retail_price);
    //set_products_pricing($pid, 'price_floor', $map);

    $cnt++;
    if($cnt==3){
        exit;
    }
}
exit;
    
/*************************************************************************************************/

function update_product($upc, $description){
    global $link;
    $description = addslashes($description);
    $sql = "UPDATE products SET title = '$description' where upc_code='$upc'";
    //echo "$sql\n";
    $res = mysql_query($sql,$link);
    if(!$res){
        echo "Query failed.\n";
        echo "$sql\n";
        exit;
        //return false;
    }
}

function set_global_user($brand){
    global $cache;
    global $link2;
    $guid = get_user_id($brand);
    if(empty($guid)){
        $cache = array();
        list($first_name, $last_name) = returnSplit($brand);
        $data = array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'company' => $brand,
            'created_at' => '2014-11-11 11:11:11',
            'is_active' => 1,
            'terms_accepted' => 0,
            'user_type' => 0,
            'is_invited' => 1,
            'session_id' =>  md5(uniqid())
        );
        $guid = dbinsert($link2, 'users', $data);
    }
    if(!$guid){
        var_dump($guid); exit;
    }
    return $guid;
}
function get_user_id($brand){
    global $link2;
    $brand = addslashes($brand);
    $where = "company='{$brand}'";
    return dbselect($link2, 'users', 'id', $where);
}


function set_production_user($brand, $guid){
    global $link;
    $uid = get_production_user_id($guid);
    if(empty($uid)){
        list($first_name, $last_name) = returnSplit($brand);
        $data = array(
            'first_name' => $first_name,
            'global_user_id' => $guid,
            'last_name' => $last_name,
            'signup_date' => '2014-11-11 11:11:11',
            'user_active' => 1,
            'user_name' => $brand,
            'email' => '',
            'last_login' => '2014-11-11 11:11:11',
        );
        $uid = dbinsert($link, 'users', $data);
    }
    if(!$uid){
        var_dump($uid); exit;
    }
    return $uid;
}
function get_production_user_id($guid){
    global $link;
    $where = "global_user_id={$guid}";
    return dbselect($link, 'users', 'id', $where);
}


function set_global_user_apps($guid){
    global $link2;
    if(get_global_user_apps($guid)){
        return true;
    }
    $data = array(
        'uid' => $guid,
        'app_name' => 'stickyvision',
        'is_invited' => 1,
        'is_allowed' => 1,
    );
    $id = dbinsert($link2, 'global_user_apps', $data);
    if(!$id){
        var_dump($id); exit;
    }
}
function get_global_user_apps($guid){
    global $link2;
    $where = "uid={$guid}";
    return dbselect($link2, 'global_user_apps', 'id', $where);
}


function set_store($brand, $uid){
    global $link;
    $sid = get_store_id($brand);
    if(empty($sid)){
        $data = array(
            'created_at' => '2014-11-11 11:11:11',
            'has_product' => 1,
            'store_enable' => 1,
            'note_enable' => 1,
            'store_name' => $brand,
            'user_id' => $uid,
            'crawl_frequency' => 'daily',
        );
        $sid = dbinsert($link, 'store', $data);
        if(!$sid){
            var_dump($sid); exit;
        }
    }
    return $sid;
}
function get_store_id($brand){
    global $link;
    $brand = addslashes($brand);
    $where = "store_name='{$brand}' AND store_enable = '1'";
    return dbselect($link, 'store', 'id', $where);
}


function set_users_store($sid, $uid){
    global $link;
    $id = get_users_store_id($sid, $uid);
    if(empty($id)){
        $data = array(
            'store_id' => $sid,
            'user_id' => $uid,
        );
        $id = dbinsert($link, 'users_store', $data);
        if(!$id){
            var_dump($id); exit;
        }
    }
    return $id;
}
function get_users_store_id($sid, $uid){
    global $link;
    $where = "store_id={$sid} AND user_id = {$uid}";
    return dbselect($link, 'users_store', 'id', $where);
}



function set_brand_columns($sid){
    global $link;
    $id = get_brand_columns_id($sid);
    if(empty($id)){
        $dataspec = array(1,2,4,5,6,7);
        $i=0;
        foreach($dataspec as $colId){
            $data = array(
                'column_id' => $colId,
                'user_store_id' => $sid,
                'sort' => $i,
            );
            $id = dbinsert($link, 'brand_columns', $data);
            if(!$id){
                var_dump($id); exit;
            }
            $i++;
        }
    }
    return $id;
}
function get_brand_columns_id($sid){
    global $link;
    $where = "user_store_id={$sid}";
    return dbselect($link, 'brand_columns', 'id', $where);
}

// "Promotional Pricing" 
function set_products_pricing($pid, $pricing_type, $pricing_value){
    $data = array(
        'product_id' => $pid,
        'pricing_type' => $pricing_type,
        'pricing_value' => $pricing_value,
        'pricing_start' => '2014-11-11 11:11:11',
    );
    $prid = dbinsert($link, 'products_pricing', $data);
    if(!$prid){
        var_dump($prid); exit;
    }
}

function set_product($upc, $sid, $description, $brand, $upc, $sku, $wholesale_price, $retail_price){
    global $link;
    $pid = get_product_id($upc, $sid);
    if(empty($pid)){
        $data = array(
            'created_at' => '2014-11-11 11:11:11',
            'is_archived' => 0,
            'is_processed' => 0,
            'is_tracked' => 1,
            'is_violated' => 0,
            'price_floor' => 0,
            'retail_price' => $retail_price,
            'sku' => $sku,
            'status' => 1,
            'store_id' => $sid,
            'title' => $description,
            'upc_code' => $upc,
            'wholesale_price' => $wholesale_price,
        );
        $pid = dbinsert($link, 'products', $data);
        if(!$pid){
            var_dump($pid); exit;
        }
    }
    return $pid;
}
function get_product_id($upc, $sid){
    global $link;
    $where = "upc_code LIKE '{$upc}' AND store_id = {$sid}";
    return dbselect($link, 'products', 'id', $where, false);
}




function dbinsert($link, $table, $data){
    $sql = "INSERT INTO $table SET ";
    $delim = '';
    foreach($data as $k=>$v){
        $v = addslashes($v);
        $sql .= $delim . " $k = '$v'";
        $delim = ',';
    }
    $res = null;
    $res = mysql_query($sql,$link);
    if(!$res){
        echo "problem with insert failure";
        echo "$sql;\n";
        exit;
        //return false;
    }
    return mysql_insert_id($link);
}

function dbselect($link, $table, $index, $where, $cache_me=true){
    global $cache;
    $sql = "SELECT $index FROM $table WHERE $where LIMIT 1";
    if(isset($cache[$sql])){
        return $cache[$sql];
    }
    $data = dbquery($link, $sql, $index);
    if(!empty($data) and $cache_me){
        $cache[$sql] = $data;
    }
    return $data;
}

function dbquery($link, $sql, $index){
    $res = mysql_query($sql,$link);
    if(!$res){
        echo "Query failed.\n";
        echo "$sql\n";
        exit;
        //return false;
    }
    $row = mysql_fetch_array($res);
    $data = trim($row[$index]);
    return $data;
}

function parse_csv_file($csvfile) {
    $csv = Array();
    $rowcount = 0;
    $delimiter = ',';
    if(strpos($csvfile, '.tsv')){
        $delimiter = "\t";
    }
    if (($handle = fopen($csvfile, "r")) !== FALSE) {
        $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
        $header = fgetcsv($handle, $max_line_length, $delimiter);
        $header = array_map('trim', $header);
        $header = array_map('ucfirst', $header);
        $header_colcount = count($header);
        while (($row = fgetcsv($handle, $max_line_length, $delimiter)) !== FALSE) {
            $row_colcount = count($row);
            if ($row_colcount == $header_colcount) {
                $entry = array_combine($header, $row);
                $entry = array_map('trim', $entry);
                $csv[] = $entry;
            }
            else {
                print("csvreader: Invalid number of columns at line " . ($rowcount + 2) . " (row " . ($rowcount + 1) . "). Expected=$header_colcount Got=$row_colcount \n");
                return null;
            }
            $rowcount++;
        }
        echo " Total $rowcount rows found\n";
        fclose($handle);
    }
    else {
        print("csvreader: Could not read CSV \"$csvfile\" \n");
        return null;
    }
    return $csv;
}

function returnSplit($str, $lines=2, $delimiter='^', $array=true){
    $strlength = strlen($str);
    $halfway = floor($strlength / $lines);
    $result = explode($delimiter, wordwrap($str, $halfway, $delimiter));
    @list($first_name, $last_name, $tail) = $result;
    $last_name = (strlen($tail)>0) ? $last_name ." ".$tail : $last_name;
    echo "$first_name, $last_name\n";
    return array($first_name, $last_name);
}

?>
