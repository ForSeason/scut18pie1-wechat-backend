<?php

namespace Modules;

class Route {
    public $responded = false;

    public function router($postObj) {
        // self::responseInstructions($postObj);
        // $MS = new ModeSwitcher($postObj);
        // $this->responded = $MS->process($postObj);
        // if ($this->responded) exit;
        
        // $redis = new \Predis\Client();
        // if ($redis->exists($postObj->FromUserName.':mode')) {
        //     $mode = $redis->get($postObj->FromUserName.':mode');
        //     if ($mode == 'maogai') {
        //         $maogai = new Maogai($postObj);
        //         $maogai->process($postObj);
        //     }
        //     exit;
        // }
        
        $static_text = new StaticText();
        $this->responded = $static_text->roll($postObj);
        if ($this->responded) exit;

        $maogai = new Maogai($postObj);
        $this->responded = $maogai->process($postObj);
        if ($this->responded) exit;

        $user_info = new UserInfo($postObj);
        $this->responded = $user_info->process($postObj);
        if ($this->responded) exit;
        
        $voting = new Voting($postObj);
        $this->responded = $voting->process($postObj);
        if ($this->responded) exit;

        $jw = new JWHelper($postObj);
        $this->responded = $jw->process($postObj);
        if ($this->responded) exit;

        Tuling::response($postObj);
         //$keyword='下一节课';
         //$Content=self::nextClass();
         //self::responseText($postObj,$keyword,$Content);
         //$keyword='下节课';
         //$Content=self::nextClass();
         //self::responseText($postObj,$keyword,$Content);

    }
}
