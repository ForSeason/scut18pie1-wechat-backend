<?php

namespace Models;

class Logger extends Model {
    public function record($postObj) {
        $logger = new Model();
        $time     = date("Y-m-d H:i:s",time());
        $weixinID = $postObj->FromUserName;
        $content  = $postObj->Content;
        $sql      = "INSERT INTO log(time,weixinID,content) VALUES(?,?,?);";
        $stmt     = $logger->link->prepare($sql);
        $stmt->execute(array($time, $weixinID, $content));
    }
}
