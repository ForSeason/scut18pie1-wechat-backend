<?php
require __DIR__."/../vendor/autoload.php";
require __DIR__."/../settings/general.php";

use Models\JW as JW;
use \Moell\Rss\Rss as Rss;

if (isset($_GET['weixinID'])) {
    $weixinID = $_GET['weixinID'];
} else {
    return;
}

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

//抬头简介
$item = [
    'title' => '第'.$thisweek.'周',
    'description' => '来自18级光电一班复读姬',
    'source' => [
        'value' => '',
        'attr' => [
            'url' => ''
        ]
    ]
];
$rss->item($item);

//校巴停靠
$data = $jw->school_bus();
if ($data[0]) {
    $data = $data[1];
    $str = '北区方向：';
    if ($data['N']) {
        $str .= implode(', ', $data['N']);
        $str .= '<br/>';
    } else {
        $str .= '暂无校巴<br/>';
    }
    $str .= '南门方向：';
    if ($data['S']) {
        $str .= implode(', ', $data['S']);
        $str .= '<br/>';
    } else {
        $str .= '暂无校巴';
    }
    $item = [
        'title' => '北校校巴停靠',
        'description' => $str,
        'source' => [
            'value' => '',
            'attr' => [
                'url' => ''
            ]
        ]
    ];
    $rss->item($item);
}

//下一节课
$next_class = $jw->next_class($arr);
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

//每日课表
$bo = false;
foreach($arr as $day => $lessonList) {
    if ($lessonList != array()) {
        $str = '';
        foreach ($lessonList as $lesson) {
            $str .= $lesson['name'].' '.
                $lesson['room'].' '.
                $lesson['period']."<br/>";
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
