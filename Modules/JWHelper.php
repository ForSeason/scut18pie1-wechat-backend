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
        $time     = $postObj->CreateTime;

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
        $jw = new JW($weixinID, $time);

        $pattern  = '/考试成绩/';
        if (preg_match($pattern, $input) && $jw->existence) return $this->score($jw);

        $pattern  = '/考试信息/';
        if (preg_match($pattern, $input) && $jw->existence) return $this->exam($jw);

        $pattern  = '/更新课表/';
        if (preg_match($pattern, $input) && $jw->existence) return $this->renew_schedule($jw);
        
        $pattern  = '/课表rss源/';
        if (preg_match($pattern, $input) && $jw->existence) {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || 
                (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) 
                ? 'https://' : 'http://';
            return $http_type.$_SERVER['HTTP_HOST'].'/rss_schedule.php?weixinID='.$weixinID;
        }

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
        if (!$jw->schedule_exists()) return $this->renew_schedule($jw);
        $schedule = $jw->load_schedule($thisweek);
        return $jw->format_schedule($schedule, $thisweek);
    }

    public function schedule_sp($jw, $input) {
        $pattern  = '/课表 (\d+?)/';
        $week = preg_replace($pattern, '$1', $input);
        if (!$jw->schedule_exists()) return $this->renew_schedule($jw);
        $schedule = $jw->load_schedule($week);
        return $jw->format_schedule($schedule, $week);
    }

    public function renew_schedule($jw) {
        $redis = new \Predis\Client();
        if ($redis->exists('wechat:schedule:'.$jw->weixinID.':'.$jw->time)) return '正在进行处理，请稍后。';
        $curl = curl_init();
        $query = sprintf('?weixinID=%s&time=%s', $jw->weixinID, $jw->time);
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) 
            ? 'https://' : 'http://';
        $url = $http_type.$_SERVER['HTTP_HOST'].'/renew_schedule.php';
        curl_setopt($curl, CURLOPT_URL, $url.$query);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_exec($curl);
        curl_close($curl);
        return '开始初始化课表。请等待一会再查看课表，获取速度视教务速度而定。';
    }
}
