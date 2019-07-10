<?php

namespace Models;

class JW extends Model {
    public $account;
    public $password;
    public $existence = true;

    public function __construct($weixinID) {
        $this->construct();
        $sql = "select * from jw where weixinID=?;";
        $stmt = $this->link->prepare($sql);
        $stmt->execute(array($weixinID));
        $arr = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!$arr) $this->existence = false;
        foreach ($arr as $info) {
            $this->account = $info['account'];
            $this->password = $info['password'];
        }
    }

    public static function register($weixinID, $account, $password) {
        $pdo    = new Model();
        $sql    = 'INSERT INTO jw(weixinID,account,password) VALUES(?,?,?);';
        $stmt   = $pdo->link->prepare($sql);
        $status = $stmt->execute(array($weixinID, $account, $password));
        if ($status) return 'Done.'; else return 'Unknown Error.';
    }

    public static function destroy($weixinID) {
        $pdo    = new Model();
        $sql    = 'DELETE FROM jw WHERE weixinID=?;';
        $stmt   = $pdo->link->prepare($sql);
        $status = $stmt->execute(array($weixinID));
        if ($status) return 'Done.'; else return 'Unknown Error.';
    }

    public function fetch_exam() {
        $postfield = array(
            'account' => $this->account,
            'password' => $this->password,
            'year' => EXAM_YEAR,
            'term' => EXAM_TERM
        );
        $data = $this->JWcurl('/exam', $postfield);
        if (!$data) return 'Unknown error.';
        if (array_key_exists('message', $data)) return $data['message'];
        $res = '';
        foreach ($data as $subject) {
            $res .= $subject['kcmc']."\r\n";
            $res .= $subject['cdbh']."\r\n";
            $res .= $subject['kssj']."\r\n";
            $res .= "\r\n";
        }
        return $res.'查询结束.';
    }

    public function fetch_score() {
        $postfield = array(
            'account' => $this->account,
            'password' => $this->password,
            'year' => SCORE_YEAR,
            'term' => SCORE_TERM
        );
        $data = $this->JWcurl('/score', $postfield);
        if (!$data) return 'Unknown error.';
        if (array_key_exists('message', $data)) return $data['message'];
        $res = '';
        foreach ($data['items'] as $subject) {
            $res .= $subject['kcmc']."\r\n";
            $res .= '成绩: '.$subject['cj']."\r\n";
            $res .= '绩点: '.$subject['jd']."\r\n";
            $res .= "\r\n";
        }
        return $res.'查询结束.';
    }

    public function fetch_schedule($week) {
        $postfield = array(
            'account' => $this->account,
            'password' => $this->password,
            'year' => SCHEDULE_YEAR,
            'term' => SCHEDULE_TERM
        );
        $data = $this->JWcurl('/schedule', $postfield);
        if (!$data) return 'Unknown error.';
        if (array_key_exists('message', $data)) return $data['message'];
        $schedule = $this->parse_schedule($data['kbList'], $week);
        return $schedule.'查询结束.';
    }

    public function parse_schedule($data, $thisweek) {
        $res = array(
            '星期一' => array(), 
            '星期二' => array(), 
            '星期三' => array(), 
            '星期四' => array(), 
            '星期五' => array(), 
            '星期六' => array(), 
            '星期日' => array() 
        );
        foreach($data as $lesson) {
            $name    = $lesson['kcmc'];
            $day     = $lesson['xqjmc'];
            $teacher = $lesson['xm'];
            $period  = '第'.$lesson['jcor'].'节';
            // 处理屑教务的上课周
            $week = array();
            $temp_arr = explode(',', $lesson['zcd']);
            foreach ($temp_arr as $str) {
                $pattern = '/^(\d+?)周$/';
                if (preg_match($pattern, $str)) {
                    $week[] = preg_replace($pattern, '$1', $str);
                    continue;
                }
                $pattern = '/^(\d+?)-(\d+?)周$/';
                if (preg_match($pattern, $str)) {
                    $min = preg_replace($pattern, '$1', $str);
                    $max = preg_replace($pattern, '$2', $str);
                    for ($i = $min; $i <= $max; $i++) $week[] = $i;
                    continue;
                }
                $pattern = '/^(\d+?)-(\d+?)周.+/';
                if (preg_match($pattern, $str)) {
                    $min = preg_replace($pattern, '$1', $str);
                    $max = preg_replace($pattern, '$2', $str);
                    for ($i = $min; $i <= $max; $i += 2) $week[] = $i;
                    continue;
                }
            }
            if (!in_array($thisweek, $week)) continue;
            $tmp = array($name, $period, $teacher);
            $res[$day][] = $tmp;
        }

        $head = '第'.$thisweek.'周'."\r\n";
        $str  = '';
        foreach($res as $day => $lessonList) {
            if ($lessonList != array()) {
                $str .= $day;
                $str .= "\r\n";
                foreach ($lessonList as $lesson) {
                    foreach ($lesson as $element) $str .= $element.'  ';
                    $str .= "\r\n";
                } 
                $str .= "\r\n";
            }
        }
        return ($str)? $head.$str: $head."暂无课表.\r\n";
    }

    public function JWcurl($afterfix, $postfield) {
        $url  = APIV2_BASEURL.$afterfix;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_NOBODY, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postfield));
        $json = curl_exec($curl);
        try {
            $res = json_decode($json, TRUE);
            return $res;
        } catch(Exception $e) {
            return null;
        }
    }
}
