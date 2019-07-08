<?php

namespace Models;

class Model {
    public $link;

    public function __construct() {
        try{
            $this->link = new \PDO(
                DATABASE_NAME, 
                DATABASE_ID, 
                DATABASE_PW, 
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
            );
        } catch(\PDOException $e) {
            $this->throwException($e->getMessage()); 
        }
    }
    
    public function construct() {
        try{
            $this->link = new \PDO(
                DATABASE_NAME, 
                DATABASE_ID, 
                DATABASE_PW, 
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
            );
        } catch(\PDOException $e) {
            $this->throwException($e->getMessage()); 
        }
    }

  	public function throwException($info = '') {
  		echo $info;
  	}
}
