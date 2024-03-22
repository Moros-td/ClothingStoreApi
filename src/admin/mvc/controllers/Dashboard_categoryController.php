<?php
    class Dashboard_categoryController extends Controller{
        function getAllCategories(){
            $data = [];

            $model = $this->model("Category");
            $data = $model->LoadCategories();

            //var_dump($data);
            $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo $json_response;
        }
    }
?>