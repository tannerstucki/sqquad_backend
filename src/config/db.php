<?php
    class db{
        // Properties
        private $dbhost = 'localhost';
        private $dbuser = 'sqquadx1_admin';
        private $dbpass = 'C0rnD0g*';
        private $dbname = 'sqquadx1_db';

        // Connect
        public function connect(){
            $mysql_connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname";
            $dbConnection = new PDO($mysql_connect_str, $this->dbuser, $this->dbpass);
            $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if ($dbConnection->connect_error){
                die("Connection failed: " . $dbConnection->connect_error);
            }
            return $dbConnection;
        }
    }