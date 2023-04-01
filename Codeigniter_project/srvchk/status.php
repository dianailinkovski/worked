<?php
// This script should be installed on two servers:
//      Server 1: remote server to be tested. This script must be given proper permissions chmod 777, chown root:root
//      Server 2: locally run robot_check of server 1, using cron
// In production, comment out all switches except robot_checks
// Usage:  [cron]: wget [LOCAL_HOST]/srvchk/status.php?page=robot_check
// Do NOT modify this script!!

$rHOST = "http://sv-new.juststicky.com"; 
$admin_email_to = 'chris@juststicky.com';
$admin_email_cc = 'andrew@juststicky.com';
$admin_email_cc = 'cfortune@telus.net';

error_reporting(E_ALL);
$page = isset($_GET['page']) ? $_GET['page'] : '';
switch($page){
    case 'iframe_chk':
        config_ob_flush();
        iframe_chk();
        config_ob_flush();
        break;
    case 'test_email':
        test_email();
        break;
    case 'robot_check':
        header("Content-Type: text/plain");
        local_robot_check();
        break;
    case 'remote_robot_check':
        header("Content-Type: text/plain");
        remote_robot_check();
        break;
    default:
        config_ob_flush();
        welcome();
        break;
}
exit;

/***********************************************************************/
function welcome(){
    echo html_header();
    echo '<h3>Check status of crawler services</h3>
        <p><a href="?page=test_email" target="myIframe">Emit test message</a>
        || <a href="?page=iframe_chk" target="myIframe">Check processes status</a>
        || <a href="?page=robot_check">Simulate Robot Check</a></p>
        <p><iframe name="myIframe" src="'.$GLOBALS['rHOST'].'/srvchk/status.php?page=iframe_chk" width="100%" height="100%" border="0"></iframe></p>';
    echo html_footer();
}

function test_email(){
    $message = "Message: [Simulation:] These processes failed and could not be restarted: \n [LIST OF PROCESSES]\n";
    echo html_header() . ' <PRE style="background-color: black; color: white">';
    echo "To: {$GLOBALS['admin_email_to']}\n";
    echo "Cc: {$GLOBALS['admin_email_cc']}\n";
    echo "Subject: Failed Server Process\n";
    echo $message;
    echo "(check your email)\n";
    echo '</PRE>' . html_footer();
    $sent_from = 'http://'.$_SERVER['SERVER_NAME'].'/'.$_SERVER['PHP_SELF'];
    send_email("Test from {$sent_from}\n\n $message");
}

function iframe_chk(){
    echo html_header() . ' <PRE style="background-color: black; color: white">';
    echo "<p>Crond <br/>";
    echo _web_chk('crond')."</p>";
    echo "<p>HTTP <br/>";
    echo _web_chk('httpd')."</p>";
    echo "<p>MySql <br/>";
    echo _web_chk('mysqld')."</p>";
    echo '</PRE>' . html_footer();
}

function _web_chk($srv = 'crond'){
    $output = "";
    $response = _ps($srv);
    // todo: parse response
    if(empty($response)){
        $output .= "<span style='color:red;font-weight: 900;'>NOT OK. Restarting ...</span>\n";
        $output .= _restart($srv);
        $response = _ps($srv);
    }
    if(!empty($response)){
        $output .= "<span style='color:green; font-weight: 900;'>OK</span>\n";
        $output .= $response;
    }
    else{
        send_email("This process failed and could not be automatically restarted!: {$srv}  Login and fix it.");
    }
    return $output;
}

function local_robot_check(){
    $response = file_get_contents($GLOBALS['rHOST'].'/'.$_SERVER['PHP_SELF'].'?page=remote_robot_check');
    if(empty($response)){
        send_email("HTTP failed and could not be automatically restarted!  Login and fix it.");
        echo "HTTP: NOT OK\n";
    }
    elseif(strpos($response,'NOT OK')!==FALSE){
        send_email("A process failed and could not be automatically restarted!  Login and fix it.\n\n".$response);
        echo $response;
    }
    else{
        echo $response;
    }
    die();
}

function remote_robot_check(){
    $services = array('crond','httpd','mysqld');
    foreach($services as $srv){
        $response = _ps($srv);
        if(empty($response)){
            _restart($srv);
            $response = _ps($srv);
        }
        else{
            echo "$srv: OK\n";
        }
        
        if(empty($response)){
            //send_email("This process failed and could not be automatically restarted!: {$srv}  Login and fix it.");
            echo "$srv: NOT OK\n";
        }
    }
    die();
}

function _restart($srv){
    return shell_exec("service {$srv} restart");
}

function _ps($srv){
    return shell_exec("ps aux|grep {$srv}|grep -v grep");
}

function send_email($message){
    $Name = "Process Watchdog"; //senders name
    $email = "email@adress.com"; //senders e-mail adress
    $recipient = $GLOBALS['admin_email_to']; //recipient
    $mail_body = "{$message}\n"; //mail body
    $subject = "Failed Server Process"; //subject
    $header = "From: ". $Name . " <" . $email . ">\r\n"; 
    $header .= "Cc: {$GLOBALS['admin_email_cc']}\r\n"; //optional headerfields
    
    mail($recipient, $subject, $mail_body, $header); //mail command :)
}

function html_header(){
    return "<html><body>";
}
        
function html_footer(){
    return "</body></html>\n";
}

function config_ob_flush(){
    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
    ob_implicit_flush(1);    
}

?>
