<?php
class Warning extends MY_Controller
{
    //public function Warning(){
    //    parent::__construct();
    //}
    
    public function index(){
        echo "index\n";
        exit;
    }
    public function email(){
echo "<PRE>\n";
print_r($this->db);         echo "email\n";
        exit;
    }
}

?>
