<?php

namespace Models;

class Vote extends Model {
    public $creator  = null;
    public $list     = array();
    public $class    = null;
    public $username = null;

    public function __construct($username, $class = null) {
        // if $class in database
        $this->username = $username;
        $this->construct();
        if (self::vote_exists($class)) {
            // read from database
            $this->class = $class;
            $sql  = 'SELECT * FROM vote;';
            $stmt = $this->link->query($sql);
            foreach ($stmt as $row) if ($row['class'] == $class) {
                $json = $row['json'];
                $this->creator = $row['creator'];
            }
            $this->list = json_decode($json);
        } else {
            $this->creator = $username;
            $this->class   = $class;
        }
        return true;
    }

    public function push() {
        if (in_array($this->username, $this->list) or !self::vote_exists($this->class)) {
            return false;
        } else {
            array_push($this->list, $this->username);
            $this->list = array_values($this->list);
            $this->save();
            return true;
        }
    }

    public function pop() {
        if (in_array($this->username, $this->list) and self::vote_exists($this->class)) {
            $key = array_search($this->username, $this->list);
            unset($this->list[$key]);
            $this->list = array_values($this->list); 
            $this->save();
            return true;
        } else {
            return false;
        }
    }

    public function json() {
        return json_encode($this->list);
    }

    public function save() {
        if ($this->vote_exists($this->class)) {
            $sql  = 'UPDATE vote SET json=? WHERE class=?;';
            $stmt = $this->link->prepare($sql);
            $stmt->execute(array($this->json(), $this->class));
        } else {
            // create a vote
            $sql  = 'INSERT INTO vote(json,class,creator) VALUES(?,?,?);';
            $stmt = $this->link->prepare($sql);
            $stmt->execute(array($this->json(), $this->class, $this->creator));
        }
    }

    public static function vote_exists($class) {
        $sql  = "SELECT * FROM vote;";
        $pdo  = new Model();
        $stmt = $pdo->link->query($sql);
        foreach ($stmt as $row) if ($row['class'] == $class) return true;
        return false;
    }

    public static function fetch_votes() {
        $arr  = array();
        $sql  = 'SELECT * FROM vote;';
        $pdo  = new Model();
        $stmt = $pdo->link->query($sql);
        foreach ($stmt as $row) {
            $arr[] = $row['class'];
        }
        return $arr;
    }

    public function destroy() {
        if (self::vote_exists($this->class) and ($this->username == $this->creator)) {
            $sql  = "DELETE FROM vote WHERE class=?;";
            $stmt = $this->link->prepare($sql);
            $stmt->execute(array($this->class));
            $res = $stmt->rowCount();
            if ($res == 0) {
                return false;
            } else {
                return true;
            }
        } else return false;
    }
}
