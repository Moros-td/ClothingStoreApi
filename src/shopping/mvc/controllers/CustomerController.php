<?php
    class CustomerController extends Controller{
        function getCustomerInfo(){
            $data = [];


            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $data = array(
                    "email" => $_POST['email']
                );

                $model = $this->model("Customer");
                $data = $model->FindCustomerInfo($data['email']);
    
                //var_dump($data);
                $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                echo $json_response;
            }
        }

        function updateCustomerInfo(){
            $data = [];


            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $data = array(
                    "email" => $_POST['email'],
                    "fullName" => $_POST['fullName'],
                    "phone" => $_POST['phone']
                );

                $model = $this->model("Customer");
                $data = $model->EditCustomer($data);

                if($data == "done"){
                    $response['message'] = "done";
                }
                else{
                    $response['err'] = $data;
                }
    
                //var_dump($data);
                $json_response = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                echo $json_response;
            }
        }

        function updateCustomerPassword(){
            $data = [];


            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $accountInfo = array(
                    "email" => $_POST['email'],
                    "password" => $_POST['password'],
                    "newPassword" => $_POST['newPassword']
                );

                $pass_hash = hash('sha256', $accountInfo["password"]);
                $accountInfo['password'] =  $pass_hash;

                $new_pass_hash = hash('sha256', $accountInfo["newPassword"]);
                $accountInfo['newPassword'] =  $new_pass_hash;

                $model = $this->model("Customer");
                $data = $model->checkPassword($accountInfo);
                
                if($data){
                    $data = $model->UpdatePassword($accountInfo);
                    if($data == "done"){
                        $response['message'] = "done";
                    }
                    else{
                        $response['err'] = "Lỗi!";
                    }
                }
                else{
                    $response['err'] = "Mật khẩu hiện tại không đúng!";
                }
    
                //var_dump($data);
                $json_response = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                echo $json_response;
            }
        }
    }
?>