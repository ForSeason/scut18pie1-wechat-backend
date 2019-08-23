<?php
require __DIR__."/../vendor/autoload.php";
require __DIR__."/../settings/general.php";

use Models\JW as JW;
use \Moell\Rss\Rss as Rss;

$weixinID = $_GET['weixinID'];
if (!$weixinID) return;

$jw = new JW($weixinID, 23333);
$thisweek = date("W", time()) - WEEK_START;
$rss = new Rss();

$sql = "SELECT day,name,room,period,teacher,region FROM schedule WHERE weixinID=? AND week=?";
$stmt = $jw->link->prepare($sql);
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

$next_class = next_class($arr);
if ($next_class[0]) {
    $item = [
        'title' => '下一节课',
        'description' => $next_class[1],
        'source' => [
            'value' => '',
            'attr' => [
                'url' => ''
            ]
        ]
    ];
} else {
    $item = [
        'title' => $next_class[1],
        'description' => '好好休息吧～',
        'source' => [
            'value' => '',
            'attr' => [
                'url' => ''
            ]
        ]
    ];
}
$rss->item($item);

$bo = false;
foreach($arr as $day => $lessonList) {
    if ($lessonList != array()) {
        $str = '';
        foreach ($lessonList as $lesson) {
            $str .= $lesson['name'].' '.
                $lesson['room'].' '.
                $lesson['period'].' '.
                $lesson['teacher']."<br/>";
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




public function next_class($schedule) {
    $week_days = array(
        '星期零',
        '星期一', 
        '星期二', 
        '星期三', 
        '星期四', 
        '星期五', 
        '星期六', 
        '星期日' 
    );
    $now_time = (int)date("Hi",time());
    $now_day  = (int)date("N",time());
    if (!array_key_exists($week_days[$now_day], $schedule)) return [false, '今天没有课哦～'];
    foreach ($schedule[$week_days[$now_day]] as $lesson) {
        $start_period_str = $lesson['period'];
        $pattern = '/.*?(\d).*/';
        $start_period = (int)preg_replace($pattern, '$1', $start_period_str);
        $schedule_time = json_decode(SCHEDULE_TIME, true);
        if ($schedule_time[$lesson['region']][$start_period] > $now_time) {
            $res = $lesson['name'].' '.
                $lesson['room'].' '.
                $lesson['period'].' '.
                $lesson['teacher'];
            return [true, $res];
        }
    }
    return [false, '今天已经没课啦！'];
}
