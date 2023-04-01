<?php

class Spider_Parser
{
	private $_baseUrl;
	
	public function __construct($baseUrl)
	{
		$this->_baseUrl=$baseUrl;
	}
	
	protected function extract_preg($preg,$value,&$matches=null)
	{
		if(is_array($preg))
		{
			foreach($preg as $preg_item)
			{
				try
				{
					unset($e);
//					echo "###trying $preg_item...\n";
					$ret=$this->extract_preg($preg_item, $value,$matches);
					if($ret=== null)
					{
						
						continue;
					}
//					echo "###match $ret is not";
					
					return $ret;
				}
				catch(Exception $e)
				{
					//echo "### exception". $e->getMessage() ."\n";
					continue;
				}
			}
//			echo "### no match for  $value\n";
			if(isset($e))
				throw $e;
			return $ret;
		}

		if(!preg_match($preg,$value,$matches) )
			throw new Exception(__FUNCTION__."(): '$value' does not match '$preg'");
		return isset($matches['value'])?$matches['value']:null;
	}

	protected function assertEquals($val1,$val2)
	{
		$this->assert($val1===$val2, "valuesnot equals '$val1' , '$val2'");
	}
	
	protected function assert($condition,$message=null)
	{
		if(!$condition)
			throw new Exception($message);
	}
	
	public function href2url($href,$pageUrl=null)
	{
		if($pageUrl===null)
			$pageUrl=$this->_baseUrl;
		//FIXME:assuming pageUrl ends with /
	
		if( substr($href,0,4) === 'http' )
			return $href;
		$href=preg_replace("#^/#","",$href);  //adjust root path (FIXME: relative path are also considererd root path)
	
		return $pageUrl.$href;
	}
	
	/**
	 * helper method for scraping a list of single fields with XPathHelper::subQueries()
	 */
	protected function array_merge_first_record($source, $addition)
	{
		if(!is_array($addition) || count($addition) !== 1)
			throw new Exception(__FUNCTION__.": unexpected addition " . var_export($addition, true) );
		//INFO: search an report destructive overwrite.
		foreach( array_intersect_key($source, $addition[0]) as $key => $item)
			if($item!== null)
				throw new Exception(__FUNCTION__.": addition will overwrite $key : $item => " . $addition[0][$key]);
		
		return array_merge($source,$addition[0]);
	}
	
	
	/**
	 * helper method for parsing label/value pairs in records of html nodes.
	 * @param $array of  array( 0 => ($labelname=>'label0',$valuename=>'value0'), 1 => array($labelname=>'label1',$valuename=>'value1'), ... )
	 */	
	static protected function collapseLabelValuePairs($array,$labelname='label',$valuename='value')
	{
		$result=array();
		foreach($array as $record)
		{
			$result[$record[$labelname]]=$record[$valuename];
		}
		return $result;
	}

}
