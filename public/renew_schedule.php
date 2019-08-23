<?php
require __DIR__."/../vendor/autoload.php";
require __DIR__."/../settings/general.php";

use Models\JW as JW;

$base_url = $_SERVER['HTTP_HOST']; 
$weixinID = $_POST['weixinID'];
$time     = $_POST['time'];
if (!$weixinID || !$time) return;
$redis = new \Predis\Client();
$jw    = new JW($weixinID, $time);
$redis->set('wechat:schedule:'.$jw->weixinID.':'.$jw->time, '正在进行处理');
$schedule = $jw->fetch_schedule();
$jw->save_schedule($schedule);
$redis->del('wechat:schedule:'.$jw->weixinID.':'.$jw->time);
