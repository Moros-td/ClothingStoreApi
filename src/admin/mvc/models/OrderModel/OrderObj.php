<?php
class OrderObj {
    public $orderCode;
    public $orderDate;
    public $state;
    public $totalPrice;
    public $listOrder;

    public function __construct($row) {
        $this->orderCode = $row['order_code'];
        $this->orderDate = $row['order_date'];
        $this->totalPrice = $row['total_price'];
        $listOrder = stripslashes($row['list']);
        $list_array = json_decode($listOrder,true);
        if ($list_array!==null){
            $this->listOrder = $list_array;
        }
        $this->state = $row['state'];
    }
   
    public function getOrderCode() {
        return $this->order_code;
    }

    public function setOrderCode($order_code) {
        $this->order_code = $order_code;
    }

    public function getOrderDate() {
        return $this->order_date;
    }

    public function setOrderDate($order_date) {
        $this->order_date = $order_date;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function getTotalPrice() {
        return $this->total_price;
    }

    public function setTotalPrice($total_price) {
        $this->total_price = $total_price;
    }
}

?>
