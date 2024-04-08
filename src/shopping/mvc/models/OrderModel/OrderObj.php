<?php
    class OrderObj{
        public $order_code;
        public $order_date;
        public $state;
        public $total_price;
        public $email;
        public $address;

        public function __construct($row)
    {
        $this->order_code = $row['order_code'];
        $this->order_date = $row['order_date'];
        $this->state = $row['state'];
        $this->total_price = $row['total_price'];
        $this->email = $row['email'];
        $this->address = $row['address'];
    }
    public function getOrder_code()
    {
            return $this->order_code;
    }

    public function setOrder_code($order_code)
    {
            $this->order_code = $order_code;
    }

    public function getOrder_date()
    {
            return $this->order_date;
    }

    public function setOrder_date($order_date)
    {
            $this->order_date = $order_date;
    }
    public function getState()
    {
            return $this->state;
    }

    public function setState($state)
    {
            $this->state = $state;
    }

    public function getTotal_price()
    {
            return $this->total_price;
    }

    public function setTotal_price($total_price)
    {
            $this->total_price = $total_price;
    }
    public function getEmail()
    {
            return $this->email;
    }

    public function setEmail($email)
    {
            $this->email = $email;
    }

    public function getAddress()
    {
            return $this->address;
    }

    public function setAddress($address)
    {
            $this->address = $address;
    }
    }
?>
