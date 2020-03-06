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
            $this->link->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch(\PDOException $e) {
            http_response_code(500);
            $this->throwException($e->getMessage()); 
            exit();
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
            $this->link->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        } catch(\PDOException $e) {
            $this->throwException($e->getMessage()); 
            exit();
        }
    }

  	public function throwException($info = '') {
  		echo json_encode(['message' => $info]);
  	}
}
