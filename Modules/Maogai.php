<?php

namespace Modules;
use Models\User  as User;

class Maogai {
    protected $redis;
    protected $user;

    public function __construct($postObj) {
        $this->user      = new User($postObj->FromUserName);
        $this->redis = new \Predis\Client();
    }

    public function process($postObj) {
        $res = $this->roll($postObj);
        if ($res) Wechat::responseText($postObj, $res);
        return ($res)? true: false;
    }

    public function roll($postObj) {
        $input    = $postObj->Content;
        $weixinID = $postObj->FromUserName;
        if (User::weixinID2username($weixinID) == 'unknown') return '非注册用户不得使用该功能！';

        $pattern  = '/^[A-Za-z]+$/';
        if (preg_match($pattern, $input)) return $this->answer($input);

        return $this->repeat();
    }

    public function answer($input) {
        $input = strtoupper($input);
        if (!$this->redis->exists($this->user->weixinID.':maogai:'.MAOGAI_FILE.':id')) return '系统出错： 缺失毛概标识符';
        $id = $this->redis->get($this->user->weixinID.':maogai:'.MAOGAI_FILE.':id');
        $pattern = '/([A-Za-z])/';
        preg_match_all($pattern, $input, $arr);
        $a = [];
        foreach ($arr[1] as $v) {
            $a[] = $v;
        }
        sort($a);
        $q = self::getData();
        $out = fopen(__DIR__.'/../public/assets/'.date('Ymd').'.csv', 'a+');
        if ($a == $q[$id]['answer']) {
            fputcsv($out, [
                $q[$id]['number'],
                $q[$id]['type'],
                $this->user->weixinID2username($this->user->weixinID),
                $input,
                '正确'
            ]);
        } else {
            fputcsv($out, [
                $q[$id]['number'],
                $q[$id]['type'],
                $this->user->weixinID2username($this->user->weixinID),
                $input,
                '错误'
            ]);
        }
        fclose($out);
        if (!isset($q[++$id])) {
            $this->redis->del($this->user->weixinID.':mode');
            $this->redis->del($this->user->weixinID.':maogai:'.MAOGAI_FILE.':id');
            $this->redis->set($this->user->weixinID.':completed:'.MAOGAI_FILE, 1);
            return '答题完毕，退出答题模式。';
        }
        $this->redis->set($this->user->weixinID.':maogai:'.MAOGAI_FILE.':id', $id);
        $result = $q[$id]['type'].'#'.$q[$id]['title']."\r\n";
        foreach ($q[$id]['questions'] as $v) {
            $result .= $v['option'].". ".$v['content']."\r\n";
        }
        return $result;
    }

    public function repeat() {
        $id = $this->redis->get($this->user->weixinID.":maogai:".MAOGAI_FILE.":id") ?: 0;
        $q = self::getData();
        $result = $q[$id]['type'].'#'.$q[$id]['title']."\r\n";
        foreach ($q[$id]['questions'] as $v) {
            $result .= $v['option'].". ".$v['content']."\r\n";
        }
        return $result;
    }

    public static function init($weixinID) {
        $redis = new \Predis\Client();
        $redis->set($weixinID.':maogai:'.MAOGAI_FILE.':id', 0);
        $redis->set($weixinID.':mode', 'maogai');
        $q = self::getData();
        $result = $q[0]['type'].'#'.$q[0]['title']."\r\n";
        foreach ($q[0]['questions'] as $v) {
            $result .= $v['option'].". ".$v['content']."\r\n";
        }
        return $result;
    }

    public static function getData() {
        $redis = new \Predis\Client();
        if ($redis->exists('maogai:'.MAOGAI_FILE)) {
            return unserialize($redis->get('maogai:'.MAOGAI_FILE));
        }
        $data = require_once __DIR__.'/../scripts/maogai.php';
        $redis->set('maogai:'.MAOGAI_FILE, serialize($data));
        return $data;
    }
}
