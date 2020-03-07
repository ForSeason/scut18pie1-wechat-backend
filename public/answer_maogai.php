<?php
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../settings/general.php';
require __DIR__.'/../settings/authority.php';

use \Models\User;

$user = new User('123');

$data = file_get_contents('php://input');
$data = json_decode($data, TRUE);

if (!MAOGAI_ON || 
    !$data ||
    !isset($data['weixinID']) || 
    $user->weixinID2username($data['weixinID']) == 'unknown'
) {
    header('HTTP/1.1 403 Forbidden');
    echo 'GUNAAAAAAAA~~~~!!!';
    exit;
}

$redis = new \Predis\Client();

if ($redis->exists($data['weixinID'].":completed:".MAOGAI_FILE)) {
    header('HTTP/1.1 403 Forbidden');
    echo '不能重复做题！！！！！';
    exit;
}

$answers = $data['answer'] ?? [];
$origin = require_once __DIR__.'/../scripts/maogai.php';
$out = fopen(__DIR__.'/assets/'.date('Ymd').'.csv', 'a+');
foreach ($answers as $ans) {
    foreach ($origin as $q) {
        if ($ans['type'] == $q['type'] && $ans['number'] == $q['number']) {
            if ($ans['answer'] == $q['answer']) {
                fputcsv($out, [
                    $ans['number'],
                    $ans['type'],
                    $user->weixinID2username($data['weixinID']),
                    implode("", $ans['answer']),
                    '正确',
                    ($ans['type'] == '单选')? 2: 4
                ]);
            } else {
                fputcsv($out, [
                    $ans['number'],
                    $ans['type'],
                    $user->weixinID2username($data['weixinID']),
                    implode("", $ans['answer']),
                    '错误',
                    0
                ]);
            }
        }
    }
}
fclose($out);

header('HTTP/1.1 200 OK');
$redis->set($data['weixinID'].":completed:".MAOGAI_FILE, 1);
