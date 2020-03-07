<?php
include_once __DIR__."/docx2text.php";
include_once __DIR__."/../settings/general.php";

$redis = new \Predis\Client();
if ($redis->exists(MAOGAI_FILE)) {
    return json_decode($redis->get(MAOGAI_FILE), true);
}

$text = new Docx2Text();
$text->setDocx(__DIR__.MAOGAI_FILE);
$docx = $text->extract();
// $docx = urldecode($docx);

// 分离题目
$pattern1 = '/\d+?、[\s\S]*?答案[\s\S]*?[A-Za-z]+/u';
preg_match_all($pattern1, $docx, $rawQ);

//解析题目
$question = [];
$pattern2 = '/(\d+)、(.*)/u';
$pattern3 = '/([A-Za-z])[ 、，。,．\.\t]+([^ \n\r]+)/u';
$pattern4 = '/答案.*?([A-Za-z]+)/u';
$pattern5 = '/([A-Za-z])/u';
foreach($rawQ[0] as $str) {
    preg_match_all($pattern2, $str, $arr2);
    preg_match_all($pattern3, $str, $arr3);
    preg_match_all($pattern4, $str, $arr4);
    preg_match_all($pattern5, $arr4[1][0], $arr5);
    $q = [];
    foreach($arr3[1] as $k => $v) {
        $q[] = ['option' => $arr3[1][$k], 'content' => $arr3[2][$k]];
    }
    $a = [];
    foreach($arr5[1] as $v) {
        $a[] = strtoupper($v);
    }
    sort($a);
    $question[] = [
        'number' => $arr2[1][0],
        'title'  => $arr2[2][0],
        'questions' => $q,
        'answer' => $a,
        'type' => isset($a[1])? '多选': '单选',
    ];
}

$redis->set(MAOGAI_FILE, json_encode($question));

return $question;
