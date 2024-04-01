<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

    class AuthController extends Controller{
        function Login(){

            // lấy và validate data
            if(isset($_POST['username']) && isset($_POST['password'])){
                $data['username'] = $_POST['username'];
                $password = $_POST['password'];
                $pass_hash = hash('sha256', $password);
                $data['password'] = $pass_hash;
            }

            $model = $this->model("Admin");
            $result = $model->checkAccount($data);
            
            if($result != null){

                if($result['err'] == null){

                    $key = getenv('key_api');
            
                    $payload = [
                        'iss' => 'http://localhost:8097',
                        'aud' => 'http://localhost:8097',
                        'iat' => time(),
                        'nbf' => time(),
                        'exp' => time() + (60 * 60 * 24 * 7),
                        'username' => $data['username'],
                        'role' => $result['role']
                    ];
    
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    $data["token"] = $jwt;
                    $data["username"] = $data['username'];
                    $model = $this->model("Token");

                    $checkUserHaveToken = $model->checkUserHaveToken($data["username"]);
                    if(is_bool($checkUserHaveToken)){
                        if($checkUserHaveToken){
                            if($model->DeleteToken($data["username"]) != "done"){
                                http_response_code(401);
                                $response["err"] = "Something wrong";
                                $json_response = json_encode($response);
        
                                // Trả về dữ liệu JSON
                                echo $json_response;
                                return;
                            }
                        }
    
                        $result = $model->InsertToken($data);

                        if($result == "done"){
                            $response["token"] = $jwt;
                        }
                        else{
                            http_response_code(401);
                            $response["err"] = "Something wrong";
                        }
                    }
                }
                else{
                    $response['err'] = $result['err'];
                    http_response_code(401);
                }
        
                $json_response = json_encode($response);
        
                // Trả về dữ liệu JSON
                echo $json_response;
            }
            else{
                $response["err"] = "Wrong username or password";
                http_response_code(401);
                $json_response = json_encode($response);
                echo $json_response;
            }
        }

        public function Logout($params){
			// Hủy tất cả các biến session
            $model = $this->model("Token");
			$res = $model->DeleteToken($params["account_info"]->username);
            if($res == "done"){
                $response["message"] = "Success";
                $json_response = json_encode($response);
                echo $json_response;
            }
            else{
                $response["err"] = "Something wrong";
            }
		}

        public function Test($params){
            echo "Have token";
        }
    }
?>