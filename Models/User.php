<?php

namespace Models;

class User extends Model {
    public $authority;
    public $weixinID;

    public function __construct($weixinID) {
        $this->construct();
        $this->weixinID  = (string)$weixinID;
        $this->authority = $this->getAuthority();
    }

    public function dormitory($input) {
        $str      = strtoupper($input);
        $domitory = substr($str, 0, 2);
        $room     = substr($str, 2, 3);
        $sql      = "SELECT * FROM user WHERE domitory=? AND room=?";
        $stmt     = $this->link->prepare($sql);
        $stmt->execute(array($domitory, $room));
        $arr      = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $res      = '';
        foreach ($arr as $row) {
            if ($row['domitoryMaster'] == 'NO') {
                $res .= $row['username']."\r\n";
            } else {
                $res .= $row['username']."(舍长)\r\n";
            }
        }
        return ($res == '')? 'No result.': $res."查询结束.";
    }

    public function fetchInfo($username) {
        $sql  = "SELECT * FROM user WHERE username=?;";
        $stmt = $this->link->prepare($sql);
        $stmt->execute(array($username));
        $arr  = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $res  = '';
        foreach ($arr as $row) {
            $res .= '姓名：'.$row['username']."\r\n";
            $res .= '性别：'.$row['sex']."\r\n";
            $res .= '民族：'.$row['ethnicity']."\r\n";
            $res .= '学号：'.$row['studentID']."\r\n";
            $res .= '身份证号：'.$row['ID_card']."\r\n";
            $res .= '宿舍：'.$row['domitory'].$row['room']."\r\n";
            $res .= '是否舍长：'.$row['domitoryMaster']."\r\n";
            $res .= '电话：'.$row['phone']."\r\n";
            $res .= '家长姓名：'.$row['parentName']."\r\n";
            $res .= '家长电话：'.$row['parentPhone']."\r\n";
            $res .= '生源地：'.$row['fromWhere']."\r\n";
            $res .= '政治面貌：'.$row['politicalStatus']."\r\n";
            $res .= '家庭住址：'.$row['adress']."\r\n";
            $res .= '邮政编码：'.$row['postalcode']."\r\n";
        }
        return ($res == '')? 'No result.': $res."查询结束.";
    }

    public function weixinID2username($weixinID) {
        $sql  = "SELECT * FROM user WHERE weixinID=?;";
        $pdo  = new Model();
        $stmt = $pdo->link->prepare($sql);
        $stmt->execute(array($weixinID));
        $arr  = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $username = 'unknown';
        foreach($arr as $row) $username = $row['username'];
        return $username;
    }

    public function username2WeixinID($username) {
        $sql  = "SELECT * FROM user WHERE username=?;";
        $pdo  = new Model();
        $stmt = $pdo->link->prepare($sql);
        $stmt->execute(array($username));
        $arr  = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $weixinID = 'unknown';
        foreach($arr as $row) $weixinID = $row['weixinID'];
        return $weixinID;
    }

    public function getAuthority() {
        $authlist = json_decode(AUTHORITY_LIST, true);
        if (array_key_exists($this->weixinID, $authlist)) {
            return $authlist[$this->weixinID];
        } else {
            return 1;
        }
    }

    public function getAllUsernames() {
        $pdo  = new Model();
        $sql  = "SELECT username FROM user";
        $stmt = $pdo->link->query($sql);
        $res  = '';
        foreach ($stmt as $row) $res .= $row['username'].' ';
        return $res;
    }

    public function user_exists($weixinID) {
        $pdo  = new Model();
        $sql  = "SELECT * FROM user WHERE weixinID=?;";
        $stmt = $pdo->link->prepare($sql);
        $stmt->execute(array($weixinID));
        $arr  = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return ($arr)? 1: 0;
    }
}
