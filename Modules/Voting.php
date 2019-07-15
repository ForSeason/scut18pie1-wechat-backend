<?php

namespace Modules;
use Models\User as User;
use Models\Vote as Vote;

class Voting {
    
    public function process($postObj) {
        $res = $this->roll($postObj);
        if ($res) Wechat::responseText($postObj, $res);
        return ($res)? true: false;
    }

    public function roll($postObj) {
        $input    = $postObj->Content;
        $weixinID = $postObj->FromUserName;
        $username = User::weixinID2username($weixinID);

        /*
         *  不是注册用户不能使用投票功能
         */
        if (!User::user_exists($weixinID)) return false;

        /*
         *  下面开始检测用户是否使用如下业务
         */
        $pattern  = '/^发起投票 ([\s\S]+)$/';
        if (preg_match($pattern, $input)) return $this->createVote($input, $username);

        $pattern  = '/^删除投票 ([\s\S]+)$/';
        if (preg_match($pattern, $input)) return $this->deleteVote($input, $username);

        $pattern  = '/^投票 ([\s\S]+)$/';
        if (preg_match($pattern, $input)) return $this->vote($input, $username);

        $pattern  = '/^查看投票 ([\s\S]+)$/';
        if (preg_match($pattern, $input)) return $this->getVoteInfo($input, $username);

        $pattern  = '/^撤销投票 ([\s\S]+)$/';
        if (preg_match($pattern, $input)) return $this->unVote($input, $username);

        $pattern  = '/^查看所有投票$/';
        if (preg_match($pattern, $input)) return $this->fetchVotes();

        return false;
    }

    public function createVote($input, $creator) {
        $pattern = '/^发起投票 ([\s\S]+)$/;';
        $class   = preg_replace($pattern, '$1', $input);
        if (Vote::vote_exists($class)) {
            $res = 'Fatel error: Vote exists.';
        } else {
            $vote = new Vote($creator, $class);
            $vote->save();
            $res  = 'Done.';
        }
        return $res;
    }

    public function deleteVote($input, $username) {
        $pattern = '/^删除投票 ([\s\S]+)$/;';
        $class   = preg_replace($pattern, '$1', $input);
        $vote    = new Vote($username, $class);
        $status  = $vote->destroy();
        $res     = ($status)? 'Done.': 'Fatal error: Vote does not exists OR Permission denied.';
        return $res;
    }

    public function vote($input, $username) {
        $pattern = '/^投票 ([\s\S]+)$/;';
        $class   = preg_replace($pattern, '$1', $input);
        $vote    = new Vote($username, $class);
        $status  = $vote->push();
        $res     = ($status)? 'Done.': 'Fatal error: Vote does not exists OR Permission denied.';
        return $res;
    }

    public function unVote($input, $username) {
        $pattern = '/^撤销投票 ([\s\S]+)$/;';
        $class   = preg_replace($pattern, '$1', $input);
        $vote    = new Vote($username, $class);
        $status  = $vote->pop();
        $res     = ($status)? 'Done.': 'Fatal error: Vote does not exists OR Permission denied.';
        return $res;
    }

    public function fetchVotes() {
        $arr = Vote::fetch_votes();
        return ($arr == array())? 'No result.': implode(', ', $arr);
    }

    public function getVoteInfo($input, $username) {
        $pattern = '/^查看投票 ([\s\S]+)$/;';
        $class   = preg_replace($pattern, '$1', $input);
        if (!Vote::vote_exists($class)) {
            $res = 'Fatel error: Vote does not exist.';
        } else {
            $vote = new Vote($creator, $class);
            $res  = '发起人: '.$obj_vote->creator."\r\n".count($obj_vote->list).'人: ';
            $res .= (implode(', ',$obj_vote->list) == '')?'no member': implode(', ',$obj_vote->list);
        }
        return $res;
    }
}
