<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CommentController extends Controller
{
    function getAllCommentForProduct(){
        $data = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $request = array(
                "productCode" => $_POST['productCode']
            );
        $model = $this->model("Comment");
        $data = $model->LoadAllCommentForProduct($request['productCode']);

        $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_response;
        }
    }

    function addComment(){
        $data = [];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $request = array(
                "email" => $_POST['email'],
                "productCode" => $_POST['productCode'],
                "rating" => $_POST['rating'],
                "comment" => $_POST['comment'],
                "orderItemId" => $_POST['orderItemId']
            );

        $model = $this->model("Comment");
        $data = $model->addComment($request);

        if($data == "done"){
            $response['message'] = "done";
        }
        else{
            $response['err'] = $data;
        }
        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_response;
        }
    }
}