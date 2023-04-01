<?php

class Spider_Controller
{

	private $_baseUrl;
	private $_parser;
	
	/**
	 * 
	 * @param string $baseUrl format http://host.domain.com/
	 * @param Spider_Parser $parser
	 */
	public function __construct($baseUrl,Spider_Parser $parser)
	{
		$this->_baseUrl=$baseUrl;
		$this->_parser=$parser;
	}
	
	protected function getParser()
	{
		return $this->_parser;
	}
	
	public function getBaseUrl()
	{
		return $this->_baseUrl;
	}
	
	
	public function openHref( $href, $pageUrl=null )
	{
		$href=preg_replace("/#.*$/","",$href); //remove "fragment" part of href
		return new XPathHelper( $this->getParser()->href2url($href,$pageUrl) );
	}
}