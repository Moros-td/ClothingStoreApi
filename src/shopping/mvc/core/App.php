<?php

    class App extends MiddleWare{
        private $controller = "";
        private $action = "";
        private $params = [];
        function __construct() {
            $arr = $this->UrlProcess();

            // find Controllers
            if(isset($arr[0])){
                $arr[0] .= "Controller";
                if(file_exists("./mvc/controllers/" . $arr[0] . ".php")){
                    $this->controller = $arr[0];
                    //echo $this->controller . " " . $arr[0];
                }
                unset($arr[0]);
            }
            
            if(empty($this->controller)){
                http_response_code(403);
                require_once "./mvc/403.php";
                return;
            }

            $validAccess = false;
            $whileList = ['HomeController', 'ProductController', 'CategoryController', 'AuthController', 'TestController', 'CommentController'];

            // kiểm tra các trang được vào mà không đăng nhập
            if (in_array($this->controller, $whileList)) {
                $validAccess = true;
            }

            $controllerStr =  $this->controller;
            require_once "./mvc/controllers/" . $this->controller . ".php";

            // khởi tạo để lát gọi phương thức
            $this->controller = new $this->controller();

            // find Action (Method)
            if(isset($arr[1])){
                if(method_exists($this->controller, $arr[1])){
                    if(!method_exists('Controller', $arr[1])){
                        $this->action = $arr[1];
                    }
                }
                unset($arr[1]);
            }

            if(empty($this->action)){
                http_response_code(403);
                require_once "./mvc/403.php";
                return;
            }
            
            // find Params
            // nếu $arr != null thì lấy arr, không thì lấy mảng rỗng
            $this->params["params_url"] = $arr?array_values($arr):[];

        //    print_r($this->params);
        //    print_r($this->action);
        //    print_r($this->controller);
        //    die($this->controller);
            $account_info = $this->checkAuth();

           // có token chưa
           if(is_object($account_info)){
                $this->params["account_info"] = $account_info;
                call_user_func([$this->controller, $this->action], $this->params);
           }
            else{
                // được vào các trang không cần đăng nhập
                if($validAccess && $this->action != "Logout"){
                    // gọi method trong class với params
                    call_user_func([$this->controller, $this->action], $this->params);
                }else{

                    $response["err"] =  $account_info;
                    $json_response = json_encode($response);
            
                    // Trả về dữ liệu JSON
                    echo $json_response;
                }
           }
        }

        function UrlProcess(){
            // /Controller/Action/Params
            if(isset($_GET['url'])){

                // cắt khoảng trắng
                // loại bỏ kí tự không hợp lệ (các kí tự không nằm trong ascii)
                // mỗi khi gặp / sẽ cắt và bỏ vào mảng
                return explode("/", filter_var(trim($_GET['url']), FILTER_SANITIZE_URL));
            }
        }

    }
?>