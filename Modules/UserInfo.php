<?php

namespace Modules;
use Models\User  as User;
use Models\Point as Point;

class UserInfo {
    public $authority;
    public $user;

    public function __construct($postObj) {
        $this->user      = new User($postObj->FromUserName);
        $this->authority = $this->user->authority;
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
        $pattern  = '/^查询 权限$/';
        if (preg_match($pattern, $input)) return $this->authority;

        $pattern  = '/^查询 weixinID$/';
        if (preg_match($pattern, $input)) return $weixinID;

        $pattern  = '/^查询 宿舍 ([a-zA-Z\d]{5})$/';
        $tempstr  = preg_replace($pattern, '$1', $input);
        if (preg_match($pattern, $input)) return $this->findDomitoryAll($tempstr);

        $pattern  = '/^查询 全信息 (.*)$/';
        $tempstr  = preg_replace($pattern, '$1', $input);
        if (preg_match($pattern, $input)) return $this->findOneAll($tempstr);
        
        $pattern  = '/^查询 我$/';
        if (preg_match($pattern, $input)) return $this->findMyselfAll($weixinID);

        $pattern  = '/^查询 德育分$/';
        if (preg_match($pattern, $input)) return $this->checkMyDP($weixinID);

        $pattern  = '/^查询 文体分$/';
        if (preg_match($pattern, $input)) return $this->checkMyWP($weixinID);

        $pattern  = '/^查询 智育分$/';
        if (preg_match($pattern, $input)) return $this->checkMyZP($weixinID);

        $pattern  = '/^查询 德育分 (.*)$/';
        $tempstr  = preg_replace($pattern, '$1', $input);
        if (preg_match($pattern, $input)) return $this->checkOneDP($tempstr);

        $pattern  = '/^查询 文体分 (.*)$/';
        $tempstr  = preg_replace($pattern, '$1', $input);
        if (preg_match($pattern, $input)) return $this->checkOneWP($tempstr);

        $pattern  = '/^查询 智育分 (.*)$/';
        $tempstr  = preg_replace($pattern, '$1', $input);
        if (preg_match($pattern, $input)) return $this->checkOneZP($tempstr);

        $pattern  = '/^加德育分 ([^ ]*?) ([^ ]*?) (.*)$/';
        if (preg_match($pattern, $input)) return $this->addDP($input);

        $pattern  = '/^加文体分 ([^ ]*?) ([^ ]*?) (.*)$/';
        if (preg_match($pattern, $input)) return $this->addWP($input);

        $pattern  = '/^加智育分 ([^ ]*?) ([^ ]*?) (.*)$/';
        if (preg_match($pattern, $input)) return $this->addZP($input);

        $pattern = '/^删除 德育分 (.*)$/';
        if (preg_match($pattern, $input)) return $this->delDP($input);

        $pattern = '/^删除 文体分 (.*)$/';
        if (preg_match($pattern, $input)) return $this->delWP($input);

        $pattern = '/^删除 智育分 (.*)$/';
        if (preg_match($pattern, $input)) return $this->delZP($input);

        $pattern  = '/^查询 事件$/';
        if (preg_match($pattern, $input)) return $this->checkEvents();

        $pattern  = '/^查询 事件 (.*)$/';
        $tempstr  = preg_replace($pattern, '$1', $input);
        if (preg_match($pattern, $input)) return Point::getEventMembers($tempstr);

        return false;
    }

    public function findDomitoryAll($dormitory) {
        return $this->user->dormitory($dormitory);
    }

    public function findOneAll($username) {
        if ($this->authority < 2) return '权限不足！';
        return $this->user->fetchInfo($username);
    }

    public function findMyselfAll($weixinID) {
        $username = $this->user->weixinID2username($weixinID);
        return $this->user->fetchInfo($username);
    }

    public function checkMyDP($weixinID) {
        return Point::fetchP($weixinID, 'd');
    }

    public function checkMyWP($weixinID) {
        return Point::fetchP($weixinID, 'w');
    }

    public function checkMyZP($weixinID) {
        return Point::fetchP($weixinID, 'z');
    }

    public function checkOneDP($username) {
        if ($this->authority < 2) return '权限不足！';
        $weixinID = User::username2WeixinID($username);
        return Point::fetchP($weixinID, 'd');
    }

    public function checkOneWP($username) {
        if ($this->authority < 2) return '权限不足！';
        $weixinID = User::username2WeixinID($username);
        return Point::fetchP($weixinID, 'w');
    }

    public function checkOneZP($username) {
        if ($this->authority < 2) return '权限不足！';
        $weixinID = User::username2WeixinID($username);
        return Point::fetchP($weixinID, 'z');
    }

    public function addDP($input) {
        if ($this->authority < 3) return '权限不足！';
        $pattern = '/^加德育分 ([^ ]*?) ([^ ]*?) (.*)$/';
        return $this->addP_handle($input, $pattern, 'd');
    }

    public function addWP($input) {
        if ($this->authority < 3) return '权限不足！';
        $pattern = '/^加文体分 ([^ ]*?) ([^ ]*?) (.*)$/';
        return $this->addP_handle($input, $pattern, 'w');
    }

    public function addZP($input) {
        if ($this->authority < 3) return '权限不足！';
        $pattern = '/^加文体分 ([^ ]*?) ([^ ]*?) (.*)$/';
        return $this->addP_handle($input, $pattern, 'z');
    }

    public function delDP($input) {
        if ($this->authority < 3) return '权限不足！';
        $pattern = '/^删除 德育分 (.*)$/';
        return $this->delP_handle($input, $pattern, 'd');
    }

    public function delWP($input) {
        if ($this->authority < 3) return '权限不足！';
        $pattern = '/^删除 文体分 (.*)$/';
        return $this->delP_handle($input, $pattern, 'w');
    }

    public function delZP($input) {
        if ($this->authority < 3) return '权限不足！';
        $pattern = '/^删除 文体分 (.*)$/';
        return $this->delP_handle($input, $pattern, 'z');
    }

    public function addP_handle($input, $pattern, $type) {
        $point   = preg_replace($pattern, '$1', $input);
        $info    = preg_replace($pattern, '$2', $input);
        $names   = preg_replace($pattern, '$3', $input);
        if (strtolower($names) == 'all') $names = User::getAllUsernames();
        $pattern = '/([^ ]+)/';
        $arr     = array();
        preg_match_all($pattern, $names, $arr);
        $res     = 0;
        foreach ($arr[0] as $username) {
            $res += Point::addP($username, $point, $info, $type);
        }
        $res = "Done.\r\n".$res." rows affected.";
        return $res;
    }

    public function delP_handle($input, $pattern, $type) {
        $IDs     = preg_replace($pattern, '$1', $input);
        $pattern = '/([^ ]+)/';
        $arr     = array();
        preg_match_all($pattern, $IDs, $arr);
        $res     = 0;
        foreach ($arr[0] as $id) {
            $res += Point::delP($id, $type);
        }
        $res = "Done.\r\n".$res." rows affected.";
        return $res;
    }

    public function checkEvents() {
        $res  = '德育分:'."\r\n\r\n";
        $res .= Point::getEvents('d');
        $res .= "-------------------\r\n";
        $res .= '文体分:'."\r\n\r\n";
        $res .= Point::getEvents('w');
        $res .= "-------------------\r\n";
        $res .= '智育分:'."\r\n\r\n";
        $res .= Point::getEvents('z');
        return $res;
    }
}


