<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class OrderController extends Controller
{
    function AddOrder(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $order_data = [
                'state'=>$_POST['state'],                
                'total_price' => $_POST['total_price'],
                'email'=>$_POST['email'],
                'address'=>$_POST['address']
            ];
            $order_data = array_map('trim', $order_data);
            $model=$this->model("Order");
            $arr = $model->AddOrder($order_data);
            if ($arr == "lỗi") {
                echo json_encode(['error' => $arr], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo json_encode(['order_code' => $arr], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }
    function AddOrderItem(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $order_data = [
                'order_code'=>$_POST['order_code'],                
                'product_code' => $_POST['product_code'],
                'quantity'=>$_POST['quantity'],
                'size'=>$_POST['size'],
                'total_price'=>$_POST['total_price'],
            ];
            $order_data = array_map('trim', $order_data);
            $model=$this->model("Order");
            $arr = $model->AddOrderItem($order_data);
            if ($arr != "done") {
                echo json_encode(['error' => $arr], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo json_encode(['success' => 'done'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }
}