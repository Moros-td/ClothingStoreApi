<?php
include_once "./mvc/models/ProductModel/ProductObj.php";
class CartItemObj{
    public $cart_item_id;
    public $cart_code;
    public $product;
    public $quantity;
    public $size;
    public $total_price;
    public $price;

    public function __construct($row)
    {
        $this->cart_item_id = $row['cart_item_id'];
        $this->cart_code = $row['cart_code'];
        $this->quantity = $row['quantity'];
        $this->size = $row['size'];
        $this->total_price = $row['total_price'];
        $this->price = $row['price'];
    }

    public function getCart_item_id()
    {
            return $this->cart_item_id;
    }

    public function setCart_item_id($cart_item_id)
    {
            $this->cart_item_id = $cart_item_id;
    }
    public function getCart_code()
    {
            return $this->cart_code;
    }

    public function setCart_code($cart_code)
    {
            $this->cart_code = $cart_code;
    }
    public function getProduct()
    {
            return $this->product;
    }

    public function setProduct($product)
    {
            $this->product = $product;
    }
    
    public function getQuantity()
    {
            return $this->quantity;
    }

    public function setQuantity($quantity)
    {
            $this->quantity = $quantity;
    }

    public function getSize()
    {
            return $this->size;
    }

    public function setSize($size)
    {
            $this->size = $size;
    }

    public function getTotal_price()
    {
            return $this->total_price;
    }

    public function setTotal_price($total_price)
    {
            $this->total_price = $total_price;
    }

    public function getPrice()
    {
            return $this->price;
    }

    public function setPrice($price)
    {
            $this->price = $price;


    }
}

?>