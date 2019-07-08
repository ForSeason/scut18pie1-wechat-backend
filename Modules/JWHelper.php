<?php

namespace Modules;
use Models\JW as JW;

class JWHelper {
    
    public function process($postObj) {
        $res = $this->roll($postObj);
        if ($res) Wechat::responseText($postObj, $res);
        return $res? true: false;
    }

    public function roll($postObj) {
        $input    = $postObj->Content;
        $weixinID = $postObj->FromUserName;

        /*
         *  下面开始检测用户是否使用如下业务
         */
        $pattern  = '/^绑定教务 ([^ ]*?) ([^ ]*?)$/';
        if (preg_match($pattern, $input)) return $this->bindJW($input, $weixinID);

        $pattern  = '/^解除绑定教务$/';
        if (preg_match($pattern, $input)) return $this->unbindJW($weixinID);

        /*
         *  下面的业务，用户不绑定的话则无法使用
         */
        $jw = new JW($weixinID);

        $pattern  = '/考试成绩/';
        if (preg_match($pattern, $input) && $jw->existence) return $this->score($jw);

        $pattern  = '/考试信息/';
        if (preg_match($pattern, $input) && $jw->existence) return $this->exam($jw);

        $pattern  = '/课表[^\d]*$/';
        if (preg_match($pattern, $input) && $jw->existence) return $this->schedule($jw);

        $pattern  = '/课表 (\d+?)/';
        if (preg_match($pattern, $input) && $jw->existence) return $this->schedule_sp($jw, $input);

        return false;
    }

    public function bindJW($input, $weixinID) {
        $pattern   = '/^绑定教务 ([^ ]*?) ([^ ]*?)$/';
        $account   = preg_replace($pattern, '$1', $input);
        $password  = preg_replace($pattern, '$2', $input);
        return JW::register($weixinID, $account, $password);
    }

    public function unbindJW($weixinID) {
        return JW::destroy($weixinID);
    }

    public function exam($jw) {
        return $jw->fetch_exam();
    }

    public function score($jw) {
        return $jw->fetch_score();
    }

    public function schedule($jw) {
        $thisweek = date("W", time()) - WEEK_START;
        return $jw->fetch_schedule($thisweek);
    }

    public function schedule_sp($jw, $input) {
        $pattern  = '/课表 (\d+?)/';
        $thisweek = preg_replace($pattern, '$1', $input);
        return $jw->fetch_schedule($thisweek);
    }
}
