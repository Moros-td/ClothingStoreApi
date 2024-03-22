<?php 
    class AdminObj{
        private $username;
        private $role;

        public function __construct($row)
        {
            $this->username = $row['username'];
            $this->role = $row['role'];
        }

        public function getUsername()
        {
                return $this->username;
        }

        public function getRole()
        {
                return $this->role;
        }

        public function setUsername($username)
        {
                $this->username = $username;

        }

        public function setRole($role)
        {
                $this->role = $role;

        }
    };
?>