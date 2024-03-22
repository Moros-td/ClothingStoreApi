<?php
    class DB{
        public $conn;
        private $username =  'customer';
        private $password = 'day_la_customer_ptithcm';

        public function __construct() {
            //ini_set('display_errors', 'Off');
            $connectionString = "mysql:host=" . getenv('MYSQL_HOSTNAME') . ";dbname=" . getenv('MYSQL_DATABASE');
            $conn = new \PDO($connectionString, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn = $conn;
        }
        public function select($sql, $params = null)
        {
            $connect = $this->conn;
            if($params != null){

                $res = $connect->prepare($sql);
                $res->execute($params);
            }
            else {
                $res = $connect->query($sql);
            }
            $res->setFetchMode(PDO::FETCH_ASSOC);
            $connect = null;
            return $res;
        }

        public function execute($sql, $params){
            $connect = $this->conn;
            $res = $connect->prepare($sql);
            $res->execute($params);
            $connect = null;
        }

        public function getUsername()
        {
                return $this->username;
        }

        public function setUsername($username)
        {
                $this->username = $username;
        }
    }
?>