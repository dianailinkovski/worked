<?php

$hostname = "ec2-50-16-62-159.compute-1.amazonaws.com";
$hostname = "ec2-54-225-181-31.compute-1.amazonaws.com";
$username = "test_mv2";
$password = "JbfjPq5q0iOuem";
$database = "test_mv2";
$link = mysql_connect($hostname, $username, $password) or die(mysql_error());
mysql_selectdb($database, $link);


$brands = csv_to_array('Brand URL List Per Website-Complete.csv');
//print_r($brands); exit;

foreach ($brands as $row){
    $website = $row['Website'];
    $website = str_replace('www.','', $website);
    $website = str_replace('http://','', $website);
    $brands_url = str_replace('NOT FOUND','', $row['List of Brands URL']);
    if(empty($brands_url))continue;
    echo "update marketplaces set brands_url = '".$brands_url."' where display_name= '".$website."';\n";
}


function csv_to_array($filename='', $delimiter=',')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $header = NULL;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            if(!$header)
                $header = $row;
            else
                $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}

?>