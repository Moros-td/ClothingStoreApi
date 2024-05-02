<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TestController extends Controller
{
    function getAllOrders(){
        $data = [];

        // if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //     $accountInfo = array(
        //         "email" => $_POST['email']
        //     );
        $model = $this->model("Order");
        $data = $model->getAllOrders("n20dcat004@student.ptithcm.edu.vn");
        $data2 = $model->LoadOrderHistory("n20dcat004@student.ptithcm.edu.vn");
        $data3 = array_merge($data, $data2);
        //var_dump($data);
        $json_response = json_encode($data3, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_response;
        // }
    }
}