<?php 
    set_time_limit(0);
    $live_con = mysql_connect('localhost','root','XmiA3der:9vg');
    $livedb = 'test_mv2';
    $testdb = 'svtest';
    $tstore = 52;
    $dstore = 58;
    
    function insert($db,$table,$data) {
        if(is_array($data) && count($data)) {
            $insert_qry = "INSERT INTO ".$table.' SET ';
            foreach($data as $field => $value) {
                $insert_qry .= $field.'="'.$value.'",';
            }
            $insert_qry = substr($insert_qry,0,strlen($insert_qry)-1);
            mysql_db_query($db,$insert_qry);
            return mysql_insert_id();
        }
    }
    
    $product_qry = mysql_db_query($livedb, "SELECT * FROM products WHERE store_id=".$tstore);
    while($product = mysql_fetch_assoc($product_qry)) {
        $product_id = $product['id'];
        unset($product['id']);
        $product['store_id'] = $dstore;
        $new_product_id = insert($testdb,'products',$product);
        
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        $pricing_qry = mysql_db_query($livedb, "SELECT * FROM products_pricing WHERE product_id='$product_id'");
        while($pricing = mysql_fetch_assoc($pricing_qry)) {
            unset($pricing['pricing_id']);
            $pricing['product_id'] = $new_product_id;
            insert($testdb,'products_pricing',$pricing);
        }
        
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        $dailyprice_qry = mysql_db_query($livedb, "SELECT * FROM daily_price_average WHERE upc='".$product['upc_code']."'");
        while($dailyprice = mysql_fetch_assoc($dailyprice_qry)) {
            unset($dailyprice['id']);
            insert($testdb,'daily_price_average',$dailyprice);
        }
        
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        $productlist_qry = mysql_db_query($livedb, "SELECT * FROM crowl_product_list_new WHERE upc='".$product['upc_code']."'");
        while($productlist = mysql_fetch_assoc($productlist_qry)) {
            unset($productlist['id']);
            $merchant_qry = mysql_db_query($livedb, "SELECT * FROM crowl_merchant_name_new WHERE id='".$productlist['merchant_name_id']."'");
            while($prolistmerchant = mysql_fetch_assoc($merchant_qry)) {
                $merchant_exist_qry = mysql_db_query($testdb,"SELECT * FROM crowl_merchant_name_new WHERE merchant_name LIKE('".$prolistmerchant['merchant_name']."')");
                if(mysql_num_rows($merchant_exist_qry)) {
                    $merchant_id = mysql_result($merchant_exist_qry,0,0);
                }
                else {
                    unset($prolistmerchant['id']);
                    $merchant_id = insert($testdb,'crowl_merchant_name_new',$prolistmerchant);
                }
            }
            $productlist['merchant_name_id'] = $merchant_id;
            insert($testdb,'crowl_product_list_new',$productlist);
        }
        
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
    }
    $violation_qry = mysql_db_query($livedb, "SELECT * FROM violation_streaks WHERE store_id='$tstore'");
        while($violation = mysql_fetch_assoc($violation_qry)) {
        unset($violation['id']);
        $merchant_qry = mysql_db_query($livedb, "SELECT * FROM crowl_merchant_name_new WHERE id='".$violation['crowl_merchant_name_id']."'");
        while($prolistmerchant = mysql_fetch_assoc($merchant_qry)) {
            $merchant_exist_qry = mysql_db_query($testdb,"SELECT * FROM crowl_merchant_name_new WHERE merchant_name LIKE('".$prolistmerchant['merchant_name']."')");
            if(mysql_num_rows($merchant_exist_qry)) {
                $merchant_id = mysql_result($merchant_exist_qry,0,0);
            }
            else {
                unset($prolistmerchant['id']);
                $merchant_id = insert($testdb,'crowl_merchant_name_new',$prolistmerchant);
            }
        }
        $violation['store_id'] = $dstore;
        $violation['crowl_merchant_name_id'] = $merchant_id;
        insert($testdb,'violation_streaks',$violation);
    }
?>