<?php
    class DB{
        public $conn;
        private $username;
        private $password;

        public function __construct() {
            $this->switchToAdmin();
            //$this->switchToStaff();
            // if($account_info->role == 'manager'){
            //     $this->switchToManager();
            // }
            // else if($account_info->role == 'admin'){
            //     $this->switchToAdmin();
            // }
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
        public function switchToStaff()
        {
            $this->username = "staff";
            $this->password = "day_la_staff_ptithcm";
        }
        public function switchToManager()
        {
            $this->username = "manager";
            $this->password = "day_la_manager_ptithcm";
        }
        public function switchToAdmin()
        {
            $this->username = "admin";
            $this->password = "day_la_admin_ptithcm";
        }
    }
?>