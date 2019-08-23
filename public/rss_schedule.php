<?php
require __DIR__."/../vendor/autoload.php";
require __DIR__."/../settings/general.php";

use Models\JW as JW;
use \Moell\Rss\Rss as Rss;

$weixinID = $_GET['weixinID'];
if (!$weixinID) return;

$jw = new JW($weixinID, 23333);
// $thisweek = date("W", time()) - WEEK_START;
$thisweek = 1;
$rss = new Rss();

$model = new Models\Model();
$sql = "SELECT day,name,room,period,teacher FROM schedule WHERE weixinID=? AND week=?";
$stmt = $model->link->prepare($sql);
$stmt->execute([$weixinID, $thisweek]);
$arr = $stmt->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);

$channel = [
    'title' => ' ',
    'link'  => ' ',
    'description' => ' ',
    'category' => [
        'value' => 'html',
        'attr' => [
            'domain' => ''
        ]
    ]
];
$rss->channel($channel);
$item = [
    'title' => '课表    第'.$thisweek.'周',
    'description' => '来自18级光电一班复读姬',
    'source' => [
        'value' => '',
        'attr' => [
            'url' => ''
        ]
    ]
];
$rss->item($item);
$bo = false;
foreach($arr as $day => $lessonList) {
    if ($lessonList != array()) {
        $str = '';
        foreach ($lessonList as $lesson) {
            foreach ($lesson as $element) $str .= $element.'  ';
            $str .= "<br/>";
        } 
    }
    $item = [
        'title' => $day,
        'description' => $str,
        'source' => [
            'value' => '',
            'attr' => [
                'url' => ''
            ]
        ]
    ];
    $rss->item($item);
    $bo = true;
}
$item = [
    'title' => '暂无课表',
    'description' => '暂无课表',
    'source' => [
        'value' => '',
        'attr' => [
            'url' => ''
        ]
    ]
];
if (!$bo) $rss->item($item);
echo $rss;
