<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../settings/general.php';
require __DIR__.'/../settings/authority.php';

use \Models\User;

$user = new User('123');

if (!MAOGAI_ON || 
    !isset($_POST['weixinID']) || 
    $user->weixinID2username($_POST['weixinID']) == 'unknown'
) {
    header('HTTP/1.1 403 Forbidden');
    echo 'GUNAAAAAAAA~~~~!!!';
    exit;
}

$redis = new \Predis\Client();

if ($redis->exists($_POST['weixinID'].":completed:".MAOGAI_FILE)) {
    header('HTTP/1.1 403 Forbidden');
    echo '不能重复做题！！！！！';
    exit;
}

$answers = $_POST['answers'] ?? [];
$data = require_once __DIR__.'/../scripts/maogai.php';
$out = fopen(__DIR__.'/assets/'.date('Ymd').'.csv', 'a+');
foreach ($answers as $ans) {
    foreach ($data as $q) {
        if ($ans['type'] == $q['type'] && $ans['number'] == $q['number']) {
            if ($ans['answer'] == $q['answer']) {
                fputcsv($out, [
                    $q['number'],
                    $q['type'],
                    $user->weixinID2username($_POST['weixinID']),
                    $ans['answer'],
                    '正确',
                    ($q['type'] == '单选')? 2: 4
                ]);
            } else {
                fputcsv($out, [
                    $q['number'],
                    $q['type'],
                    $user->weixinID2username($_POST['weixinID']),
                    $ans['answer'],
                    '错误',
                    0
                ]);
            }
        }
    }
}

header('HTTP/1.1 200 OK');
$redis->set($_POST['weixinID'].":completed:".MAOGAI_FILE, 1);
