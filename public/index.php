<?php

require __DIR__."/../vendor/autoload.php";
require __DIR__."/../settings/general.php";
require __DIR__."/../settings/authority.php";
require __DIR__."/../settings/tuling.php";
use \Modules\Wechat as Wechat;
use \Modules\Route  as Route;
use \Models\Logger  as Logger;

$timestamp = isset($_GET['timestamp'])? $_GET['timestamp']: '';
$nonce     = isset($_GET['nonce'])? $_GET['nonce']: '';
$token     = TOKEN;
$signature = isset($_GET['signature'])? $_GET['signature']: '';
$echostr   = isset($_GET['echostr'])? $_GET['echostr']: ''; 
$array     = array($timestamp, $nonce, $token);
sort($array); 
$tmpstr = implode('', $array);
$tmpstr = sha1($tmpstr);   
if ($tmpstr == $signature && $echostr) {         
    echo $echostr;    
    exit;  
} else { 
    $postArr = file_get_contents('php://input');
    $postObj = simplexml_load_string($postArr);
    if (!$postObj) {
        echo '<h1>GUNA!!!!!!</h1>';
        exit;
    }
    //file_put_contents('arr.txt', $postObj);
    Logger::record($postObj);
    if (strtolower($postObj->MsgType == 'event')) {
        Wechat::responseSubscribe($postObj);
    }


    if (strtolower($postObj->MsgType) == 'text') {
        $route = new Route;
        $route->router($postObj);
        // Wechat::responseKeyWords($postObj);
        // Wechat::responseTuling($postObj);     
    }

    if (strtolower($postObj->MsgType) == 'image') {
        Wechat::responseDefaut2($postObj);     
    }
}
