<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../settings/general.php';
require __DIR__.'/../settings/authority.php';

use \Models\User;

$user = new User('123');

if (!MAOGAI_ON || 
    !isset($_GET['weixinID']) || 
    $user->weixinID2username($_GET['weixinID']) == 'unknown'
) {
    header('HTTP/1.1 403 Forbidden');
    echo 'GUNAAAAAAAA~~~~!!!';
    exit();
}

$redis = new \Predis\Client();

if ($redis->exists($_GET['weixinID'].":completed:".MAOGAI_FILE)) {
    header('HTTP/1.1 403 Forbidden');
    echo '不能重复做题！！！！！';
    exit();
}

header('HTTP/1.1 200 OK');
$data = require_once __DIR__.'/../scripts/maogai.php';
$data = json_encode($data,320);
echo $data;
