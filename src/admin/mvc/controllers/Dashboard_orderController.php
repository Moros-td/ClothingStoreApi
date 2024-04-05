<?php

class Dashboard_orderController extends Controller {
    function getOrders(){
        $data = [];

        $model = $this->model("Order");
        $data = $model->LoadOrders();

        //var_dump($data);
        $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_response;
    }
}
?>
