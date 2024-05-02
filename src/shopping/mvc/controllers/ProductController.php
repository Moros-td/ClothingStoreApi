<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

    class ProductController extends Controller{
        function getAllProducts() {
            $model = $this->model("Product");
            $data = $model->LoadProducts();

            //var_dump($data);
            $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo $json_response;
        }   
        function GetProductByCategory()
        {
            $model = $this->model("Product");
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $category_id = $_POST['category_id'];
                $data = $model->FindProductsByCategory($category_id);
    
                //var_dump($data);
                $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                echo $json_response;
            }
        }
    }
?>