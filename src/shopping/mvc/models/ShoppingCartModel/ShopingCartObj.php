<?php
class ShoppingCartObj{
    public $cart_code;
    public $email;
    public function __construct($row)
    {
        $this->cart_code = $row['cart_code'];
        $this->email = $row['email'];
    }
    public function getCart_code()
    {
            return $this->cart_code;
    }

    public function setCart_code($cart_code)
    {
            $this->cart_code = $cart_code;
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
?>