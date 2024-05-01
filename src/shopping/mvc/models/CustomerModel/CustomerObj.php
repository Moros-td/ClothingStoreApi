<?php
    class CustomerObj{
        public $email;
        public $full_name;
        public $phone;

        public function __construct($row)
        {
            $this->email = $row['email'];
            $this->full_name = $row['full_name'];
            $this->phone = $row['phone'];
        }

        public function getFull_name()
        {
                return $this->full_name;
        }

        public function setFull_name($full_name)
        {
                $this->full_name = $full_name;
        }

        public function getPhone()
        {
                return $this->phone;
        }

        public function setPhone($phone)
        {
                $this->phone = $phone;
        }

        public function getEmail()
        {
                return $this->email;
        }

        public function setEmail($email)
        {
                $this->email = $email;
        }
    }

    class Verify{
        private $email;
        private $token;
        private $count;
        private $create_time;
        private $update_time;
        private $used;

        public function __construct($row)
        {
            $this->email = $row['email'];
            $this->token = $row['token'];
            $this->count = $row['count'];
            $this->create_time = $row['create_time'];
            $this->update_time = $row['update_time'];
            $this->used = $row['used'];
        }

        public function getEmail()
        {
                return $this->email;
        }

        public function setEmail($email)
        {
                $this->email = $email;
        }

        public function getCount()
        {
                return $this->count;
        }

        public function setCount($count)
        {
                $this->count = $count;
        }
        

        public function getCreate_time()
        {
                return $this->create_time;
        }


        public function setCreate_time($create_time)
        {
                $this->create_time = $create_time;
        }
        
        public function getUpdate_time()
        {
                return $this->update_time;
        }

        public function setUpdate_time($update_time)
        {
                $this->update_time = $update_time;

        }

        public function getToken()
        {
                return $this->token;
        }

        public function setToken($token)
        {
                $this->token = $token;
        }


        public function getUsed()
        {
                return $this->used;
        }
        public function setUsed($used)
        {
                $this->used = $used;
        }
    }
?>