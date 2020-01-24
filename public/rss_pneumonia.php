<?php
require __DIR__."/../vendor/autoload.php";
use \Moell\Rss\Rss as Rss;

// 1.获取数据
$url = 'https://3g.dxy.cn/newh5/view/pneumonia';
$header = array(
"content-type: application/x-www-form-urlencoded; 
charset=UTF-8"
);

$curl = curl_init();

curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 8);
curl_setopt($curl, CURLOPT_TIMEOUT, 10);
$html = curl_exec($curl);
// echo $html; return;

$patterns = [
    'TimelineService' => '/getTimelineService = (\[.*?\])}catch/',
    'AreaStat' => '/getAreaStat = (\[.*?\])}catch/',
    'Time' => '/截至 (.*) 数据统计/',
];

$resArr = [];
foreach ($patterns as $title => $pattern) {
    try {
        preg_match_all($pattern, $html, $arr);
        // var_dump($arr);
        $tmp = json_decode($arr[1][0], 320);
        $resArr[$title] = is_array($tmp)? $tmp: $arr[1][0];
    } catch(Exception $e) {
        $resArr[$title] = [];
    }
    reset($arr);
}

// 2.开始组装rss推送

$rss = new Rss();
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
    'title' => '全国新型肺炎动态',
    'description' => '来自18级光电一班复读姬',
    'source' => [
        'value' => '',
        'attr' => [
            'url' => ''
        ]
    ]
];
$rss->item($item);

//分割线
$item = [
    'title' => '---各省感染人数---',
    'description' => '',
    'source' => [
        'value' => '',
        'attr' => [
            'url' => ''
        ]
    ]
];
$rss->item($item);

//各省感染人数

foreach ($resArr['AreaStat'] as $pro) {
    $title = $pro['provinceName'].$pro['confirmedCount'].'人';
    $discription = '';
    foreach ($pro['cities'] as $city) {
        $discription .= $city['cityName'].$city['confirmedCount'].'人<br/>';
    }
    $item = [
        'title' => $title,
        'description' => $discription,
        'source' => [
            'value' => '',
            'attr' => [
                'url' => ''
            ]
        ]
    ];
    $rss->item($item);
}

//分割线
$item = [
    'title' => '---微博动态---',
    'description' => '',
    'source' => [
        'value' => '',
        'attr' => [
            'url' => ''
        ]
    ]
];
$rss->item($item);

//微博动态

foreach ($resArr['TimelineService'] as $timeline) {
    $title = $timeline['title'];
    $discription = $timeline['summary'];
    $item = [
        'title' => $title,
        'description' => $discription,
        'source' => [
            'value' => '',
            'attr' => [
                'url' => ''
            ]
        ]
    ];
    $rss->item($item);
}

echo $rss;
