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

    function getAllOrders(){
        $data = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $accountInfo = array(
                "email" => $_POST['email']
            );
        $model = $this->model("Order");
        $data = $model->getAllOrders($accountInfo['email']);
        $data2 = $model->LoadOrderHistory($accountInfo['email']);
        $data3 = array_merge($data, $data2);
        //var_dump($data);
        $json_response = json_encode($data3, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_response;
        }
    }

    function cancelOrder(){
        $data = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $request = array(
                "orderCode" => $_POST['orderCode']
            );
        $model = $this->model("Order");
        $data = $model->CancelOrder($request);

        if($data == "done"){
            $response["message"] = "done";
        }
        else{
            $response["err"] = $data;
        }

        //var_dump($data);
        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_response;
        }
    }
}