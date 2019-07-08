<?php

namespace Models;

class Point extends Model {

    /*
     *  $type可选参数：d或者w，
     *  dp代表德育分，wp代表文体分
     */
    public function fetchP($weixinID, $type) {
        $pdo  = new Model();
        $sql  = "SELECT * FROM ".$type."p WHERE weixinID=?;";
        $stmt = $pdo->link->prepare($sql);
        $stmt->execute(array($weixinID));
        $arr  = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $res  = '';
        foreach ($arr as $row) {
            $res.='ID:'.$row[$type.'pID'].'/'.$row['point'].'分/'.$row['info']."\r\n";
        }
        return ($res == '')? 'No result.': $res."查询结束.";
    }

    public function addP($username, $point, $info, $type) {
        $weixinID = User::username2WeixinID($username);
        $res = 0;
        if ($weixinID != 'unknown'){
            $pdo  = new Model();
            $sql  = "INSERT INTO ".$type."p(weixinID,point,info) VALUES(?,?,?);";
            $stmt = $pdo->link->prepare($sql);
            $res += $stmt->execute(array($weixinID, $point, $info));
        }
        return $res;
    }

    public function delP($id, $type) {
        $res  = 0;
        $pdo  = new Model();
        $sql  = "DELETE FROM ".$type."p WHERE ".$type."pID=?;";
        $stmt = $pdo->link->prepare($sql);
        $res += $stmt->execute(array($id));
        return $res;
    }

    public function getEvents($type) {
      $sql  = 'SELECT * FROM '.$type.'p;';
      $pdo  = new Model();
      $stmt = $pdo->link->query($sql);
      $arr  = array();
      $res  = '';
      foreach ($stmt as $row) {
        if (!array_key_exists($row['info'], $arr)) {
          $arr[$row['info']] = 1;
        }
      }
      foreach ($arr as $info => $names) {
        $res .= $info."\r\n";
      }
      return $res;
    }

    public function getEventMembers($info) {
        $pdo  = new Model();
        $sql  = "SELECT weixinID FROM dp WHERE info=?;";
        $stmt = $pdo->link->prepare($sql);
        $stmt->execute(array($info));
        $arr  = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $res  = '';
        foreach ($arr as $row) {
            $res .= User::weixinID2username($row['weixinID'])."\r\n";
        }
        $sql  = "SELECT weixinID FROM wp WHERE info=?;";
        $stmt = $pdo->link->prepare($sql);
        $stmt->execute(array($info));
        $arr  = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($arr as $row) {
            $res .= User::weixinID2username($row['weixinID'])."\r\n";
        }
        return ($res == '')? 'No result.': $res;
    }
}
