<?php
 /* ***************************************************************************
 *  CommandlineHelpers.php: utility for command line interface of scrapers
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
function print_records($records, $fields_filter= null,$record_init=array())
{
	if(count($records)===0)
		return;
		
	static $header=null;
	if( $fields_filter===null )
		if( is_array( reset($records)) )
			$fields_filter= array_keys( reset($records) );
		elseif ( is_object( reset($record) ) )
		{
			$fields_filter=array_keys(get_object_vars(reset($records)));
			echo "retrieving object fields... ".var_export($fields_filter);
		}
		else
			throw new Exception(__FUNCTION__.": unexpected record type ". gettype(reset($records)));
	
		
	if($header!==array_merge(array_keys($record_init),$fields_filter))
	{
		$header=array_merge(array_keys($record_init),$fields_filter);
		echo implode("\t",$header)."\n";
	}
	
	foreach($records as $pr)
	{
		$data= $record_init;
		foreach( $fields_filter
			 as $field)
			 {
			 	if(is_array($pr))
				 	$data[$field]=isset($pr[$field])?$pr[$field]:"NULL";
				elseif(is_object($pr))
					$data[$field]=isset($pr->$field)?$pr->$field:"NULL";
				else
					throw new Exception(__FUNCTION__.": unexpected record type ". gettype($pr));
			 }
		foreach($data as &$field)
			if(is_array($field))
				$field= "array[".count($field)."]";
			else 
				$field=formatCSV($field);
		echo implode("\t", $data). "\n";
	}
}

function formatCSV($field)
{
	$field=str_replace('"', '""', $field);
	$field=str_replace("\n", '\n', $field);
	if(strpos($field," ") !==false)
		$field='"'.$field.'"';
	return $field;
}
 
?>
