<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Proxy_ips_m extends MY_Model
{
    var $proxy;
    var $proxy_host;
    var $proxy_port;
    var $user;
    var $pass;
    
    // TODO: get this working with database and admin back end
    public function set_random_proxy(){
        // Temporary for when we have new block of IPs
        //$this->load_one_proxy_at_a_time();
        
        //SELECT *, (fails/connects) as failsScore FROM test_mv2.proxy_ips where use_flag=1 order by failsScore, rand() limit 1;
        // TODO: upgrade proxy_ips statistics to support multiple marketplaces
        // GROUP BY `proxy_port` causes the same ip every time!!!
        
		if(time()%2==0){ // randomize 50/50 chance of luminati
		//if(false){ // using luminati 100% now
        $q = "SELECT `proxy_host`, `proxy_port`, `user`, `pass`
            FROM (`proxy_ips`)
            WHERE `use_flag` = 1
                AND `last_warn_time` < '".date('Y-m-d H:i:s', strtotime('-5 minutes'))."'
            ORDER BY RAND()
            LIMIT 1";
        }
        else{
            // Luminati.io
            $this->db->cache_on();
            $this->db->mysql_cache();
            $q = "SELECT `proxy_host`, `proxy_port`, `user`, `pass`
                FROM (`proxy_ips`)
                WHERE `proxy_port`=22225";
        }

        $res = $this->db->query($q)->result();
        $this->db->cache_off();
        log_message('info', 'set_random_proxy sql: '.$this->db->last_query());
        //print_r($res); //exit;
        $temp = $res[0];
        
        //$temp->proxy_host = $this->luminati($temp); // our local failure stats won't work if we get a dynamic IP every time
        //echo "proxy: ".print_r($temp,true)."\n";
        
        $this->proxy_host = isset($temp->proxy_host) ? $temp->proxy_host : 'localhost';
        $this->proxy_port = isset($temp->proxy_port) ? $temp->proxy_port : '80';
        $this->user       = isset($temp->user) ? $temp->user : '';
        $this->pass       = isset($temp->pass) ? $temp->pass : '';
        
        $this->cookieName = md5($this->proxy_host);
    }
    
    function luminati($temp){
        if($temp->proxy_port==22225)
            return `curl -s "client.luminati.io/api/get_super_proxy?raw=1&user={$temp->user}&key={$temp->pass}"`;
        else
            return $temp->proxy_host;
    }
    
    public function bad_proxy($info, $user_agent){
        // log bad IP in db
        $this->increment_fail_count($info, $user_agent);
        
        // TODO: is it smart or dumb to delete a cookie here?
        //if(file_exists('/tmp/'. $this->cookieName .'cookie.txt')){
        //    @unlink('/tmp/'. $this->cookieName .'cookie.txt');
        //}
        
        // get another one
        $this->set_random_proxy();
        return;
    }

    public function increment_connection_count(){
        $sql = "update {$this->_table_proxy_ips}
                SET connects=connects+1
                WHERE proxy_host='{$this->proxy_host}'";
        $this->db->query($sql);
        log_message('debug', 'increment_connection_count() sql: '.$sql);
    }
    
    public function increment_fail_count($info='', $user_agent=''){
        $sql = "update {$this->_table_proxy_ips}
                SET fails=fails+1, last_warn_time=NOW(), ban_source='{$info}', ban_agent='{$user_agent}'
                WHERE proxy_host='{$this->proxy_host}'";
        $this->db->query($sql);
        log_message('debug', 'increment_fail_count() sql: '.$sql);
    }

    function load_one_proxy_at_a_time(){    
        // 23.81.206.58:29842:aschydlo:yj9cPb07 is reserved for amazon_violator_proxy 
        
        $proxies =
        "23.80.156.212:29842:aschydlo:yj9cPb07
23.106.166.100:29842:aschydlo:yj9cPb07
23.106.205.158:29842:aschydlo:yj9cPb07
23.81.206.233:29842:aschydlo:yj9cPb07
172.255.94.125:29842:aschydlo:yj9cPb07
23.108.96.30:29842:aschydlo:yj9cPb07
23.80.156.233:29842:aschydlo:yj9cPb07
23.106.166.27:29842:aschydlo:yj9cPb07
23.106.205.99:29842:aschydlo:yj9cPb07
23.81.206.70:29842:aschydlo:yj9cPb07
172.255.94.115:29842:aschydlo:yj9cPb07
23.108.96.49:29842:aschydlo:yj9cPb07
23.80.156.210:29842:aschydlo:yj9cPb07
23.106.166.203:29842:aschydlo:yj9cPb07
23.106.205.212:29842:aschydlo:yj9cPb07
23.81.206.90:29842:aschydlo:yj9cPb07
172.255.94.220:29842:aschydlo:yj9cPb07
23.108.96.61:29842:aschydlo:yj9cPb07
23.80.156.182:29842:aschydlo:yj9cPb07
23.106.166.249:29842:aschydlo:yj9cPb07
23.106.205.116:29842:aschydlo:yj9cPb07
23.81.206.149:29842:aschydlo:yj9cPb07
172.255.94.152:29842:aschydlo:yj9cPb07
23.108.96.215:29842:aschydlo:yj9cPb07
23.80.156.170:29842:aschydlo:yj9cPb07
23.106.166.199:29842:aschydlo:yj9cPb07
23.106.205.121:29842:aschydlo:yj9cPb07
23.81.206.62:29842:aschydlo:yj9cPb07
172.255.94.181:29842:aschydlo:yj9cPb07
23.108.96.138:29842:aschydlo:yj9cPb07
23.80.156.106:29842:aschydlo:yj9cPb07
23.106.166.115:29842:aschydlo:yj9cPb07
23.106.205.26:29842:aschydlo:yj9cPb07
23.81.206.74:29842:aschydlo:yj9cPb07
172.255.94.52:29842:aschydlo:yj9cPb07
23.108.96.24:29842:aschydlo:yj9cPb07
23.80.156.149:29842:aschydlo:yj9cPb07
23.106.166.139:29842:aschydlo:yj9cPb07
23.106.205.57:29842:aschydlo:yj9cPb07
23.81.206.218:29842:aschydlo:yj9cPb07
172.255.94.73:29842:aschydlo:yj9cPb07
23.108.96.128:29842:aschydlo:yj9cPb07
23.80.156.188:29842:aschydlo:yj9cPb07
23.106.166.43:29842:aschydlo:yj9cPb07
23.106.205.221:29842:aschydlo:yj9cPb07
23.81.206.32:29842:aschydlo:yj9cPb07
172.255.94.16:29842:aschydlo:yj9cPb07
23.108.96.199:29842:aschydlo:yj9cPb07
23.80.156.96:29842:aschydlo:yj9cPb07
23.106.166.239:29842:aschydlo:yj9cPb07
23.106.205.89:29842:aschydlo:yj9cPb07
23.81.206.249:29842:aschydlo:yj9cPb07
172.255.94.149:29842:aschydlo:yj9cPb07
23.108.96.6:29842:aschydlo:yj9cPb07
23.80.156.206:29842:aschydlo:yj9cPb07
23.82.110.233:29842:aschydlo:yj9cPb07
23.106.83.205:29842:aschydlo:yj9cPb07
23.81.207.99:29842:aschydlo:yj9cPb07
64.120.61.103:29842:aschydlo:yj9cPb07
23.81.237.77:29842:aschydlo:yj9cPb07
23.106.16.53:29842:aschydlo:yj9cPb07
64.120.33.141:29842:aschydlo:yj9cPb07
23.106.28.124:29842:aschydlo:yj9cPb07
23.105.142.28:29842:aschydlo:yj9cPb07
23.80.146.102:29842:aschydlo:yj9cPb07
23.82.110.230:29842:aschydlo:yj9cPb07
23.106.83.252:29842:aschydlo:yj9cPb07
23.81.207.45:29842:aschydlo:yj9cPb07
64.120.61.9:29842:aschydlo:yj9cPb07
23.81.237.66:29842:aschydlo:yj9cPb07
23.106.16.83:29842:aschydlo:yj9cPb07
64.120.33.225:29842:aschydlo:yj9cPb07
23.106.28.54:29842:aschydlo:yj9cPb07
23.105.142.212:29842:aschydlo:yj9cPb07
23.80.146.243:29842:aschydlo:yj9cPb07
23.82.110.141:29842:aschydlo:yj9cPb07
23.106.83.233:29842:aschydlo:yj9cPb07
23.81.207.34:29842:aschydlo:yj9cPb07
64.120.61.4:29842:aschydlo:yj9cPb07
23.81.237.246:29842:aschydlo:yj9cPb07
23.106.16.64:29842:aschydlo:yj9cPb07
64.120.33.102:29842:aschydlo:yj9cPb07
23.106.28.72:29842:aschydlo:yj9cPb07
23.105.142.74:29842:aschydlo:yj9cPb07
23.80.146.28:29842:aschydlo:yj9cPb07
23.82.110.42:29842:aschydlo:yj9cPb07
23.106.83.239:29842:aschydlo:yj9cPb07
23.81.207.121:29842:aschydlo:yj9cPb07
64.120.61.254:29842:aschydlo:yj9cPb07
23.81.237.144:29842:aschydlo:yj9cPb07
23.106.16.67:29842:aschydlo:yj9cPb07
64.120.33.206:29842:aschydlo:yj9cPb07
23.106.28.121:29842:aschydlo:yj9cPb07
23.105.142.158:29842:aschydlo:yj9cPb07
23.80.146.86:29842:aschydlo:yj9cPb07
23.82.110.248:29842:aschydlo:yj9cPb07
23.106.83.26:29842:aschydlo:yj9cPb07
23.81.207.38:29842:aschydlo:yj9cPb07
64.120.61.129:29842:aschydlo:yj9cPb07
23.81.237.34:29842:aschydlo:yj9cPb07
23.106.16.111:29842:aschydlo:yj9cPb07
64.120.33.145:29842:aschydlo:yj9cPb07
23.106.28.169:29842:aschydlo:yj9cPb07
23.105.142.147:29842:aschydlo:yj9cPb07
23.80.146.58:29842:aschydlo:yj9cPb07
23.82.110.102:29842:aschydlo:yj9cPb07
23.106.83.117:29842:aschydlo:yj9cPb07
23.81.207.53:29842:aschydlo:yj9cPb07
64.120.61.14:29842:aschydlo:yj9cPb07
23.81.237.134:29842:aschydlo:yj9cPb07
23.106.16.135:29842:aschydlo:yj9cPb07
64.120.33.120:29842:aschydlo:yj9cPb07
23.106.28.70:29842:aschydlo:yj9cPb07
23.105.142.102:29842:aschydlo:yj9cPb07
23.80.146.172:29842:aschydlo:yj9cPb07
23.82.110.163:29842:aschydlo:yj9cPb07
23.106.83.38:29842:aschydlo:yj9cPb07
23.81.207.136:29842:aschydlo:yj9cPb07
64.120.61.27:29842:aschydlo:yj9cPb07
23.81.237.251:29842:aschydlo:yj9cPb07
23.106.16.34:29842:aschydlo:yj9cPb07
64.120.33.165:29842:aschydlo:yj9cPb07
23.106.28.146:29842:aschydlo:yj9cPb07
23.105.142.112:29842:aschydlo:yj9cPb07
23.80.146.213:29842:aschydlo:yj9cPb07
23.82.110.3:29842:aschydlo:yj9cPb07
23.106.83.170:29842:aschydlo:yj9cPb07
23.81.207.223:29842:aschydlo:yj9cPb07
64.120.61.85:29842:aschydlo:yj9cPb07
23.81.237.49:29842:aschydlo:yj9cPb07
23.106.16.164:29842:aschydlo:yj9cPb07
64.120.33.184:29842:aschydlo:yj9cPb07
23.106.28.159:29842:aschydlo:yj9cPb07
23.105.142.129:29842:aschydlo:yj9cPb07
23.80.146.228:29842:aschydlo:yj9cPb07
23.82.110.30:29842:aschydlo:yj9cPb07
23.106.83.196:29842:aschydlo:yj9cPb07
23.81.207.14:29842:aschydlo:yj9cPb07
64.120.61.229:29842:aschydlo:yj9cPb07
23.81.237.62:29842:aschydlo:yj9cPb07
23.106.16.117:29842:aschydlo:yj9cPb07
64.120.33.192:29842:aschydlo:yj9cPb07
23.106.28.178:29842:aschydlo:yj9cPb07
23.105.142.250:29842:aschydlo:yj9cPb07
23.80.146.53:29842:aschydlo:yj9cPb07
23.82.110.201:29842:aschydlo:yj9cPb07
23.106.83.231:29842:aschydlo:yj9cPb07
23.81.207.62:29842:aschydlo:yj9cPb07
64.120.61.190:29842:aschydlo:yj9cPb07
23.81.237.59:29842:aschydlo:yj9cPb07
23.106.16.69:29842:aschydlo:yj9cPb07
64.120.33.162:29842:aschydlo:yj9cPb07
23.106.28.101:29842:aschydlo:yj9cPb07
23.105.142.230:29842:aschydlo:yj9cPb07
23.80.146.89:29842:aschydlo:yj9cPb07
23.82.110.14:29842:aschydlo:yj9cPb07
23.106.83.7:29842:aschydlo:yj9cPb07
23.81.207.168:29842:aschydlo:yj9cPb07
64.120.61.43:29842:aschydlo:yj9cPb07
23.81.237.176:29842:aschydlo:yj9cPb07
23.106.16.250:29842:aschydlo:yj9cPb07
64.120.33.219:29842:aschydlo:yj9cPb07
23.106.28.249:29842:aschydlo:yj9cPb07
23.105.142.169:29842:aschydlo:yj9cPb07
23.80.146.170:29842:aschydlo:yj9cPb07";
            
        $arr = explode("\n", $proxies);
        $str = $arr[array_rand($arr)];
        $str = trim($str);
        // Temporary
        $this->load_new_proxy_into_db($str);
        
        //list($this->proxy_host, $this->proxy_port, $this->user, $this->pass) = explode(':', $str);
    }
    
    // Temporary: use when we have a new batch of ip addresses to insert
    function load_new_proxy_into_db($str){
        //load into db if not there yet
        $insert_ignore_flag = true;
        $obj = new stdClass();
        list($obj->proxy_host, $obj->proxy_port, $obj->user, $obj->pass) = explode(':', $str);
        $this->db->insert($this->_table_proxy_ips, $obj, $insert_ignore_flag);
        $this->db->query("update ".$this->_table_proxy_ips." set last_warn_time = SUBDATE(CURDATE(),1)");
    }
    
}