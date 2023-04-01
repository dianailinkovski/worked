<?php

class XPHException extends Exception{};

class XPathHelper
{
    protected $_xpath;
    public static $_debug = false; //activate debug logs to standard ouput
    private $_url;
    public $proxy;
    public static $_sleep;
    public static $_curlopts=array();
    public $random_user_agent;
    public $agents = array();
    public $cookies = array();
        
    /**
     * 
     * XPathHelper constructor.
     * @param unknown_type $xpath
     * @throws XPHException
     */
    public function __construct($xpath)
    {
        $this->ci =& get_instance(); // access db, etc.
		$this->ci->load->model('User_agents_m', 'UserAgents');
		$this->ci->load->model('proxy_ips_m', 'ProxyIps');
        
        // get random agent and random proxy ip
        $this->ci->UserAgents->set_random_agent();
        $this->ci->ProxyIps->set_random_proxy();
        
        // public accessor to proxy object
        $this->proxy = $this->ci->ProxyIps;

        if(is_string($xpath)){
            $this->_url=$xpath;
            $xpath= $this->url2xpath($this->_url);
        }        
        if(is_object($xpath)){
            switch(get_class($xpath)){
                case 'XPathHelper':
                    $this->_xpath= $xpath->_xpath;
                    $this->_url=$xpath->getUrl();
                    break;
                case 'DOMXPath':
                    $this->_xpath=$xpath;
                    break;
                default:
                    throw new XPHException(__FUNCTION__ . ': unexpected argument type');
                    break;
            }
        }
    }
    
    public function __toString()
    {
        return $this->_url;
    }
    public function dump()
    {
        return $this->_url . "\n" . $this->_xpath->document->SaveHTML();
    }
    
    public function getUrl()
    {
        return $this->_url;
    }
    
    public function url2xpath($url)
    {
        if(XPathHelper::$_debug>=1)
            echo "opening document: $url\n";
        //retry only for http. if not consider this is a local file
        if(0 !== strpos($url,'http') )
            return self::html2xpath(file_get_contents($url));
        
        $xpath=$this->url2xpath_simple($url);

        for($i=1.05;($i<300) and self::documentIsTemporaryError($xpath)
            ;$i=1.2*$i
        ){
            error_log("##failed loading $url , sleeping ". ($i - 1) . " and retrying....");
            sleep($i-1);
            $xpath= $this->url2xpath_simple($url);
        }
        if( self::documentIsTemporaryError($xpath) )
            throw new XPHException(__FUNCTION__ . ": mutiple retries failed to load $url aborting.");
        
        return $xpath;
    }
    
    protected static function  documentIsTemporaryError( DOMXPath $xpath = null )
    {
        if($xpath===null)
            return true;
        return false;
    }
    
    
    /**
     * 
     * Build a DOMXPath to parse an HTML string
     * @param string $html
     * @throws XPHException
     */
    public static function html2xpath($html)
    {
    if( false === $html)
            return null;
        if( !is_string($html))
            throw new XPHException('empty HTML');
        if( strlen($html)==0 )
            return null;
            
        $doc= new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);   
        libxml_use_internal_errors(false);
        $xpath= new DOMXPath($doc);
        if(XPathHelper::$_debug >=1)
            echo "loading...\n"; 
        if(XPathHelper::$_debug >=2)
            echo $doc->saveHTML();
        return $xpath;
        
    }
    
    private function url2xpath_simple($url)
    {
        return self::html2xpath($this->file_get_contents($url));
    }
   
   public function file_get_contents($url)
   {
        $ch= curl_init($url);
        
        if(!empty($this->ci->ProxyIps->proxy_host)){
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($ch, CURLOPT_PROXY          , $this->ci->ProxyIps->proxy_host.':'.$this->ci->ProxyIps->proxy_port);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD   , $this->ci->ProxyIps->user.':'.$this->ci->ProxyIps->pass);
            $cookieName = $this->ci->ProxyIps->cookieName;
        }
        else{
            $cookieName = md5(parse_url($url,PHP_URL_HOST));
        }
        
        curl_setopt_array($ch,array(
               CURLOPT_ENCODING=>'gzip',
            CURLOPT_RETURNTRANSFER=>true
            ,CURLOPT_COOKIEJAR => '/tmp/'.$cookieName.'cookie.txt'
            ,CURLOPT_COOKIEFILE => '/tmp/'.$cookieName.'cookie.txt'
            ,CURLOPT_TIMEOUT =>60
            ,CURLOPT_USERAGENT=> $this->ci->UserAgents->random_user_agent
            ,CURLOPT_PROXYAUTH=>CURLAUTH_ANY
            ,CURLOPT_HTTPHEADER=> array("Accept-Charset: utf-8;q=1,*;q=0.7")
            ,CURLOPT_FOLLOWLOCATION => true
        ));
        if(self::$_curlopts)
            curl_setopt_array($ch,self::$_curlopts);
            
        $html=curl_exec($ch);
    
        if(XPathHelper::$_sleep)
            sleep(XPathHelper::$_sleep);
        
        if($html == false){
            $this->bad_proxy('Amazon no HTML');
            error_log( "## CURLERROR http_code " . curl_getinfo($ch,CURLINFO_HTTP_CODE) ." " . curl_error($ch)
                . "\n## CURL_GETINFO " . str_replace("\n", " | ",print_r(curl_getinfo($ch) , true)) );
        }
        else{
            $this->ci->ProxyIps->increment_connection_count();
        }
        
        file_put_contents( "last_spider_lib.html","$url\n$html");
        
        return $html;
   }
   
    // wrapper function
    public function bad_proxy($info){
        $this->ci->ProxyIps->bad_proxy($info, $this->ci->UserAgents->random_user_agent);
        $this->ci->UserAgents->set_random_agent();
    }
   
    /**
     * 
     * assert() use this method only for checking document format consistency
     * @param string $flag condition
     * @param string $error_string
     */
    public function assert($flag,$error_string='unexpected document structure')
    {
        if(XPathHelper::$_debug and !$flag)
            error_log($error_string);
        if(!$flag)
            throw new XPHException($error_string);
    }
    
    public function assertEquals($v1,$v2,$error_string='values are not equal')
    {
        $this->assert($v1===$v2, $error_string ." : " 
            .  var_export($v1,true)."\t".var_export($v2,true)
            .(is_array($v1) ?
                "\n values difference: " . var_export(array_diff($v1,$v2),true)
                ."\n keys difference: " . var_export(array_diff(array_keys($v1),array_keys($v2)),true)
                :''
            )
        );
    }
    
    public function assertEqualsButEncoding($v1,$v2,$error_string='values are not equal')
    {
        $v2_fixed=mb_convert_encoding($v2,'ISO-8859-1','UTF-8');
        $this->assert($v1===$v2_fixed or $v1 === $v2, $error_string ." : " . var_export($v1,true)."\t".var_export($v2,true)."/".var_export($v2_fixed,true));
    }

    /**
     * Convert table to array
     */
    public function table2array($query)
    {
        $table = $this->xpQuery($query);
        return $this->node2array($table);
    }

    /**
     * Converts table nodes to array
     */
    private function node2array($table)
    {
        //get labels to create arrayStructure 
        $arrayStructure = $this->fetchLabels($table);
        //Support for tbody, Well code table
        if(is_object($this->xpQuery("tbody",-1,$table)->item(0)))
            $table = $this->xpQuery("tbody",-1,$table)->item(0);
            
        $tableRows = $this->xpQuery("tr",-1,$table);
        $tableArray = array();
        foreach($tableRows as $tableRow)
        {
            $rowColumns = array();
            $tableColumns = $this->xpQuery("td",-1,$tableRow);
            foreach($tableColumns as $tableColumn)
            {
                if(is_object($this->xpQuery("table",-1,$tableColumn)->item(0)))
                    $rowColumns[] = $this->node2array($this->xpQuery("table",-1,$tableColumn)->item(0));
                else
                    $rowColumns[] = $tableColumn->nodeValue;
                
            }
            if(count($rowColumns) > 0)
            {    // More columns than those in arraystructure and vis versa for else
                if(count($rowColumns) > count($arrayStructure))
                {
                    $difference = count($rowColumns) - count($arrayStructure);
                    $newarray = array_fill(  count($arrayStructure) + 1 , $difference, NULL);
                    $reviseArrayStructure = array_merge($arrayStructure,$newarray);
                    $tableArray[] = array_combine($reviseArrayStructure,$rowColumns);
                }elseif(count($rowColumns) < count($arrayStructure)){
                    $difference = count($arrayStructure) - count($rowColumns);
                    $newarray = array_fill(  count($rowColumns) + 1 , $difference, NULL);
                    $reviseRowColumns = array_merge($rowColumns,$newarray);
                    $tableArray[] = array_combine($arrayStructure,$reviseRowColumns);
                }else
                    $tableArray[] = array_combine($arrayStructure,$rowColumns);
            }
        }
        return $tableArray;
    }

    /**
     * Fetch Labels from table
     */
    private function fetchLabels($table)
    {
        $arrayStructure = array();
        //Support for thead, Well code table
        if(is_object($this->xpQuery("thead",-1,$table)->item(0)))
            $table = $this->xpQuery("thead",-1,$table)->item(0);
        $tableLabels = $this->xpQuery("tr/th",-1,$table);
        foreach($tableLabels as $tableLabel)
            $arrayStructure[] = $tableLabel->nodeValue;
        return $arrayStructure;
    }

    /**
     * 
     * execute an xpath query and check that the number of result match expectation.
     *
     * @param string $query     xpath query string
     * @param int $expectedResultCount number of DOMNode expected.  if -1 then ignore.
     * @return    null        if check failed
     *         DOMNode     if 1 result
     *         DOMNodeList    if more than 1 result
     *
     */
    public function xpQuery($query,$expectedResultCount=1,$subquery=NULL)
    {
        $nodelist= $this->_xpath->query($query,$subquery);
        
        if(self::$_debug !== false)
        {
            echo "XPATH $query  $nodelist->length results\n";
            foreach($nodelist as $node){
                $nv=$this->_xpath->document->saveXML($node);
                if(strlen($nv) >500 and self::$_debug <=1)
                    $nv=substr($nv,0,500) . " [ ... ". strlen($nv)-500 . " characters truncated ... ]";
                echo $nv . "\n";
            }
        }

        if($expectedResultCount ===-1) 
            return $nodelist;

        if($nodelist->length != $expectedResultCount)
            return null;
        
        if($nodelist->length==1)
            return $nodelist->item(0);
        else
            return $nodelist;
    }
    
    /**
     * call xpQuery and return nodeValue from DOMNode object
     * @param string query    xpath query string
     * @return DOMNode if successfull or null
     */
    public function queryValue($query,$expectedResultCount=1)
    {
        $node=$this->xpQuery($query,$expectedResultCount);
        if(is_object($node))
        {
            if(is_subclass_of($node,"DOMNode"))
                return $node->nodeValue;
    
            if( get_class($node) === "DOMNodeList")
            {
                $res= array();
                foreach($nodelist=$node as $node)
                    $res[]=$node->nodeValue;
                return $res;
            }
    
            throw new XPHException(__FUNCTION__.": unexpected value of class ".get_class($node));
        }
        else
            return $node;
    }
   
    /**
     *  query main node then call all subqueries return result as numbered array with sub arrays containing label=>value
     *  TRICK: it s trimming nodeValues
     *  @param string xpmain query for main nodes
     *  @param array xplist  array of label=>xpquery
     *  @return array    numbered records
     */
    public function xpSubQueries($xpmain,$xplist)
    {
        //FIXME: missing unit test for recursion
        $res=array();

        if(is_string($xpmain))
            $nodelist=$this->xpQuery($xpmain,-1);
        elseif(is_object($xpmain))// recursion part 1
            $nodelist=$xpmain;
        else
            throw new Exception("unexpected value");
    
        foreach( $nodelist as $node)
        {
            $record= array();
            foreach($xplist as $label=>$xpQuery)
            {
                if(is_array($xpQuery))//recursion part 2
                {
                    $record[$label]=$this->xpSubQueries($this->xpQuery($xpQuery[0],-1,$node),$xpQuery[1]);
                    continue;
                }
                elseif(is_string($xpQuery))
                {
                    $nodeleaf=$this->xpQuery($xpQuery,1,$node);
                    $record[$label]= (!is_object($nodeleaf))? null : trim($nodeleaf->nodeValue);
                    continue;
                }
            }
            $res[]=$record;
         }
         return $res;
    }

    /**
     * force requerying http server for the same page.
     */
    public function reload()
    {
        if(strlen($this->_url) === 0 )
            throw new XPHException("cannot reload because url lost"); 
        if( strpos($this->_url,'http') !== 0 )
            throw new XPHException("Cannot reload a local file");
          $this->_xpath= $this->url2xpath($this->_url);
    }
    
    
    
}

?>
