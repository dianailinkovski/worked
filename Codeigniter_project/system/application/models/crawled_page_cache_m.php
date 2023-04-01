<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crawled_page_cache_m extends MY_Model
{
    public $html = '';
	private $_old_is_deleted = false;
	
	function __construct(){
	}
    
	// find and delete old file, at all cache levels
	// TODO: add expiry tokens to file names (3d == 3 days)
	// 		-- need to modify system/database/ files.  Filename like bc210feb22ff8fb50f5f19e2312bf5a3+3d
	// `find` accepts Decimal Fractions, like -mtime +0.5 == 12 hours
	public function delete_old(){
		if(!$this->_old_is_deleted){
			$this->db->delete("crawled_page_cache", "timestamp < UNIX_TIMESTAMP()-(60*60*1)");
			$cmd = "find ".$this->db->cachedir."/* -type f -mmin +59 -exec rm {} \;";
			//echo "$cmd\n";
			shell_exec($cmd);
			$this->_old_is_deleted = true;
		}
	}
	
	public function delete($url){
        $url_hash = $this->create_hash($url);
		$this->db->delete("crawled_page_cache", "url_hash='$url_hash'");
	}
	
    public function get($url){
        $this->delete_old();
		
        $url_hash = $this->create_hash($url);
		unset($url);

        $this->db->cache_on();
		$this->db->where('url_hash', $url_hash);
		$this->db->mysql_cache(); // only on production server?
        $this->db->order_by('timestamp', 'desc');
		$data = $this->db->get("crawled_page_cache", 1)->result('array');
        $this->db->cache_off();
        
        if( !empty($data[0]['html']) and strlen($data[0]['html'])>500 ){
            $html = stripslashes($data[0]['html']);
            $this->html = stripslashes($html);
			unset($data);
            return true;
        }
        return false;
    }
    
    public function set($url, $html){
        $timestamp = time();
        $url_hash = $this->create_hash($url);
        $html = addslashes($html);
		$tmp = substr($html, 0, 780);
		if(strpos($tmp, 'Robot Check')===false){ // Amazon captcha
			$this->db->insert("crawled_page_cache", array('timestamp' => $timestamp, 'url_hash' => $url_hash, 'html' => $html));
		}
		unset($html);
    }
	
	public function create_hash($url){
		list ($url_1) = explode('#', $url); // strip #fragment off end of URL
		unset($url);
		return md5($url_1);
	}
}

//CREATE TABLE `crawled_page_cache` (
//  `timestamp` int(14) DEFAULT NULL,
//  `url_hash` char(128) DEFAULT NULL,
//  `html` text
//) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='performance cache of HTML pages'$$

?>