<?php

namespace Modules;

class Route {
    public $responded = false;

    public function router($postObj) {
        // self::responseInstructions($postObj);
        $static_text = new StaticText();
        $this->responded = $static_text->roll($postObj);
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
         //$keyword='课程表';
         //$Content="下一节课是:\n".self::nextClass()."\n\n".self::timeTable();
         //self::responseText($postObj,$keyword,$Content);
         //$keyword='课表';
         //$Content="下一节课是:\n".self::nextClass()."\n\n".self::timeTable();
         //self::responseText($postObj,$keyword,$Content);
         //$keyword='下一节课';
         //$Content=self::nextClass();
         //self::responseText($postObj,$keyword,$Content);
         //$keyword='下节课';
         //$Content=self::nextClass();
         //self::responseText($postObj,$keyword,$Content);

    }
}
