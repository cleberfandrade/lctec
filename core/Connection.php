<?php

namespace Core;

use PDO;

class Connection
{
    private $dbname = DB['DRIVER'].':host='.DB['HOSTNAME'].';dbname='.DB['DATABASE'].'';
    private $user = DB['USERNAME'];
    private $pass = DB['PASSWORD'];

    public function connect()
    {
        try {
            $conn = new PDO($this->dbname,$this->user,$this->pass);
            $conn->exec('set names utf8');
            return $conn;
        } catch (\PDOException $erro) {
                echo $erro->getMessage();
        }
    }
}