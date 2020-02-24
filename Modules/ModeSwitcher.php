<?php

namespace Modules;
use Models\User  as User;

class ModeSwitcher {
    protected $redis;
    protected $user;

    public function __construct($postObj) {
        $this->user  = new User($postObj->FromUserName);
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
        /*
         *  下面开始检测用户是否使用如下业务
         */
        $pattern  = '/^退出$/';
        if (preg_match($pattern, $input)) return $this->clear();

        $pattern  = '/^毛概$/';
        if (preg_match($pattern, $input)) return $this->maogai();
    }

    protected function clear() {
        $this->redis->del($this->user->weixinID.':mode');
        return '成功退出模式';
    }

    protected function maogai() {
        if (!MAOGAI_ON) return '现在暂未开放';
        if ($this->redis->exists($this->user->weixinID.':mode')) return '请先退出目前模式';
        if ($this->redis->exists($this->user->weixinID.':maogai:'.MAOGAI_FILE.':id')) {
            return '继续上次进度.';
        }
        if ($this->redis->exists($this->user->weixinID.':completed:'.MAOGAI_FILE)) {
            return '对不起，不能重复做题';
        }
        return Maogai::init($this->user->weixinID);
    }
}


