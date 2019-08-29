<?php

namespace Modules;
use Modules\StaticText as StaticText;

class Wechat {
    public function getAccessToken(){
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRET;
        $json=file_get_contents($url);
        $arr=json_decode($json,TRUE);
        return $arr['access_token'];
    }

    public function uploadThumb(){
        $type="thumb"; 
        //$data=file_get_contents($url);
        //file_put_contents($filename,$data);
        $filedata=array("thumb"=>new \CURLFile("pic/thumb.jpg"));
        $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".self::getAccessToken()."&type=".$type;
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        if (!empty($filedata)){
            curl_setopt($curl,CURLOPT_POST,TRUE);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$filedata);
        }
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $json=curl_exec($curl);
        curl_close($curl);
        $arr=json_decode($json,TRUE);
        return $arr['thumb_media_id'];
        //return $json;
    }

    public function uploadPic($filename){
        $type="image"; 
        //$data=file_get_contents($url);
        //file_put_contents($filename,$data);
        $filedata=array("image"=>new \CURLFile("pic/".$filename));
        $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".self::getAccessToken()."&type=".$type;
        $curl=curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        if (!empty($filedata)) {
            curl_setopt($curl,CURLOPT_POST,TRUE);
            curl_setopt($curl,CURLOPT_POSTFIELDS,$filedata);
        }
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        $json=curl_exec($curl);
        curl_close($curl);
        $arr=json_decode($json,TRUE);
        return $arr['media_id'];
        //return $json;
    }

    public function responseSubscribe($postObj){
        if (strtolower($postObj->Event == 'subscribe')){
            $content='欢迎！到之前的文章里可以查看本公众号的功能！';
            $this->responseText($postObj, $content);
        }
    }

    public function responseText($postObj, $content) {
        $toUser   = $postObj->FromUserName;
        $fromUser = $postObj->ToUserName;
        $time     = time();
        $MsgType  = 'text';
        $template = TEMPLATE_TEXT;
        $info=sprintf($template, $toUser, $fromUser, $time, $MsgType, $content);
        echo $info;
    }

    public function readFile($filename){
        $handle = fopen($filename,'r');
        $str = '';
        while (!feof($handle)) {
            $tmp = fgets($handle);
            $pattern = '/^~~/';
            if (!preg_match($pattern,$tmp)) $str .= $tmp;
        }
        return $str;
    }

}
