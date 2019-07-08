<?php

namespace Modules;
use Modules\Wechat as Wechat;

class StaticText {
    public $responded = false;
    public $data;

    public function __construct() {
        $file = file_get_contents(STATIC_TEXT);
        $this->data = ($file)? json_decode($file, true): array();
    }

    /*
     *  roll函数接收postObj，将所有静态的回复都检测一遍
     *  如果检测到关键字，则回复相应文本。
     *
     */
    public function roll($postObj) {
        foreach ($this->data as $keyword => $filename) {
            $content = Wechat::readFile($filename);
            if (!$this->responded) $this->handle($postObj, $keyword, $content);
        }
        return $this->responded;
    }

    public function handle($postObj, $keyword, $content) {
        if ($postObj->Content == $keyword) {
            Wechat::responseText($postObj, $content);
            $this->responded = true;
        }
    }
}
