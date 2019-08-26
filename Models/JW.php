<?php

namespace Models;

class JW extends Model {
    public $account;
    public $password;
    public $weixinID;
    public $time;
    public $existence = true;

    public function __construct($weixinID, $time) {
        $this->construct();
        $this->weixinID = $weixinID;
        $this->time     = $time;
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
        $postfield = array(
            'account'  => $account,
            'password' => $password
        );
        $data = JW::JWcurl('/info', $postfield);
        if (array_key_exists('message', $data)) {
            return $data['message'];
        } else {
            $pdo    = new Model();
            $sql    = 'INSERT INTO jw(weixinID,account,password) VALUES(?,?,?);';
            $stmt   = $pdo->link->prepare($sql);
            $status = $stmt->execute(array($weixinID, $account, $password));
            if ($status) {
                return 'Done.';
            } else {
                switch ($stmt->errorCode()) {
                case '23000':
                    return '已经绑定了一个账号，请先输入\'解除绑定教务\'取消旧账号绑定！';
                    break;
                default:
                    return $stmt->errorCode();
                }
            }
        }
    }

    public static function destroy($weixinID) {
        $pdo    = new Model();
        $sql    = 'DELETE FROM jw WHERE weixinID=?;';
        $stmt   = $pdo->link->prepare($sql);
        $status = $stmt->execute(array($weixinID));
        if ($status) return 'Done.';
        switch ($stmt->errorCode()) {
        default:
            return $stmt->errorCode();
        }
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

    public function fetch_schedule() {
        $postfield = array(
            'account' => $this->account,
            'password' => $this->password,
            'year' => SCHEDULE_YEAR,
            'term' => SCHEDULE_TERM
        );
        $data = $this->JWcurl('/schedule', $postfield);
        if (!$data) return 'Unknown error.';
        if (array_key_exists('message', $data)) return $data['message'];
        $schedule = $this->parse_schedule($data['kbList']);
        return $schedule;
    }

    public function parse_schedule($data) {
        $one_week = array(
            '星期一' => array(), 
            '星期二' => array(), 
            '星期三' => array(), 
            '星期四' => array(), 
            '星期五' => array(), 
            '星期六' => array(), 
            '星期日' => array() 
        );
        $res = array();
        for ($week = 0; $week <= 30; $week++) $res[] = $one_week;
        foreach($data as $lesson) {
            $name    = $lesson['kcmc'];
            $day     = $lesson['xqjmc'];
            $teacher = $lesson['xm'];
            $room    = $lesson['cdmc'];
            $region  = $lesson['xqmc'];
            $period  = '第'.$lesson['jcor'].'节';
            // 处理屑教务的上课周
            $weeks   = array();
            $temp_arr = explode(',', $lesson['zcd']);
            foreach ($temp_arr as $str) {
                $pattern = '/^(\d+?)周$/';
                if (preg_match($pattern, $str)) {
                    $weeks[] = (int)preg_replace($pattern, '$1', $str);
                    continue;
                }
                $pattern = '/^(\d+?)-(\d+?)周$/';
                if (preg_match($pattern, $str)) {
                    $min = (int)preg_replace($pattern, '$1', $str);
                    $max = (int)preg_replace($pattern, '$2', $str);
                    for ($i = $min; $i <= $max; $i++) $weeks[] = $i;
                    continue;
                }
                $pattern = '/^(\d+?)-(\d+?)周.+$/';
                if (preg_match($pattern, $str)) {
                    $min = (int)preg_replace($pattern, '$1', $str);
                    $max = (int)preg_replace($pattern, '$2', $str);
                    for ($i = $min; $i <= $max; $i += 2) $weeks[] = $i;
                    continue;
                }
            }
            $tmp = array(
                'name' => $name, 
                'room' => $room, 
                'period' => $period, 
                'teacher' => $teacher,
                'region' => $region
            );
            foreach ($weeks as $week) $res[$week][$day][] = $tmp;
        }
        return $res;
    }

    public function save_schedule($schedule) {
        $sql  = "DELETE FROM schedule WHERE weixinID=?;";
        $stmt = $this->link->prepare($sql);
        $stmt->execute([$this->weixinID]);

        $sql = "INSERT INTO schedule(weixinID,week,day,name,room,period,teacher,region) VALUES(?,?,?,?,?,?,?,?);";
        foreach ($schedule as $week => $days)
            foreach ($days as $day => $lessons)
                foreach ($lessons as $lesson) {
                    $stmt = $this->link->prepare($sql);
                    $stmt->execute([
                        $this->weixinID, 
                        $week, 
                        $day, 
                        $lesson['name'],  
                        $lesson['room'],  
                        $lesson['period'], 
                        $lesson['teacher'],
                        $lesson['region'] 
                    ]);
                }
        return true;
    }

    public function load_schedule($week) {
        $sql = "SELECT day,name,room,period,teacher,region FROM schedule WHERE weixinID=? AND week=?";
        $stmt = $this->link->prepare($sql);
        $stmt->execute([$this->weixinID, $week]);
        $arr = $stmt->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);
        return $arr;
    }

    public function format_schedule($schedule, $week) {
        $head = '第'.$week.'周'."\r\n";
        $next_class = JW::next_class($schedule);
        $next_class = $next_class[0]? '下一节课: '.$next_class[1]: $next_class[1];
        $str  = '';
        foreach($schedule as $day => $lessonList) {
            if ($lessonList != array()) {
                $str .= $day;
                $str .= "\r\n";
                foreach ($lessonList as $lesson) {
                    $str .= $lesson['name'].' '.
                        $lesson['room'].' '.
                        $lesson['period'].' '.
                        $lesson['teacher']."\r\n";
                } 
                $str .= "\r\n";
            }
        }
        return ($str)? $head.$next_class."\r\n\r\n".$str: $head."暂无课表.\r\n";
    }

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

    public function schedule_exists() {
        $sql = "SELECT * FROM schedule WHERE weixinID=?";
        $stmt = $this->link->prepare($sql);
        $stmt->execute([$this->weixinID]);
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($res as $row) return true;
        return false;
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
