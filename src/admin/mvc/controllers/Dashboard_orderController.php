<?php

class Dashboard_orderController extends Controller
{
    function getAllOrders()
    {
        $data = [];

        $model = $this->model("Order");
        $data = $model->getAllOrders();
        $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_response;
    }
    function getAllOrdersHistory()
    {
        $data = [];

        $model = $this->model("Order");
        $data = $model->LoadOrderHistory();
        $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_response;
    }
    function cancelOrder()
    {
        $data = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $request = array(
                "orderCode" => $_POST['orderCode']
            );
            $model = $this->model("Order");
            $data = $model->CancelOrder($request);

            if ($data == "done") {
                $response["message"] = "done";
            } else {
                $response["err"] = $data;
            }

            //var_dump($data);
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo $json_response;
        }
    }
    function deliveringOrder(){
        $data = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $request = array(
                "orderCode" => $_POST['orderCode']
            );
            $model = $this->model("Order");
            $data = $model->Delivering($request);

            if ($data == "done") {
                $response["message"] = "done";
            } else {
                $response["err"] = $data;
            }

            //var_dump($data);
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo $json_response;
        }
    }
    function deliveredOrder(){
        $data = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $request = array(
                "orderCode" => $_POST['orderCode']
            );
            $model = $this->model("Order");
            $data = $model->Delivered($request);

            if ($data == "done") {
                $response["message"] = "done";
            } else {
                $response["err"] = $data;
            }

            //var_dump($data);
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo $json_response;
        }
    }
}
