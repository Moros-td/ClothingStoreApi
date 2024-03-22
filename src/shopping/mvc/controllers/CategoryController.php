<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

    class CategoryController extends Controller{
        function Test() {
            $response["err"] = "Something wrong";
            $json_response = json_encode($response);
            echo $json_response;
        }   
    }
?>