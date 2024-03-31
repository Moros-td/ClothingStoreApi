<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

    class Dashboard_productController extends Controller{
        function getAllProducts() {
            $model = $this->model("Product");
            $data = $model->LoadProducts();

            //var_dump($data);
            $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo $json_response;
        }   

    }
?>