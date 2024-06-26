<?php
use Firebase\JWT\JWT;
use \Firebase\JWT\Key;

    class AuthController extends Controller{
        private $categories;
        private $access = false;

        public function __construct(){}
        
        function Login(){

            $data = [];
            $email = '';
            $password = '';
            if(isset($_POST['email']) && isset($_POST['password'])){
                $email = $_POST['email'];
                $password = $_POST['password'];
            }

            $data['email'] =  $email;
            $pass_hash = hash('sha256', $password);
            $data['password'] =  $pass_hash;

                // gọi model xử lý data
            $model = $this->model("Customer");
            $result = $model->checkAccount($data);

            if(!empty($result)){

                $key = getenv('key_api');
            
                    $payload = [
                        'iss' => 'http://localhost:8096',
                        'aud' => 'http://localhost:8096',
                        'iat' => time(),
                        'nbf' => time(),
                        'exp' => time() + (60 * 60 * 24 * 7),
                        'email' => $data['email'],
                        'full_name' => $result['full_name'],
                        'cart_code' => $result['cart_code']
                    ];
    
                    $jwt = JWT::encode($payload, $key, 'HS256');
                    $data["token"] = $jwt;
                    $data["email"] = $data['email'];
                    $model = $this->model("Token");

                    $checkUserHaveToken = $model->checkUserHaveToken($data["email"]);
                    if(is_bool($checkUserHaveToken)){
                        if($checkUserHaveToken){
                            if($model->DeleteToken($data["email"]) != "done"){
                                http_response_code(401);
                                $response["err"] = "Có gì đó không ổn, vui lòng thử lại";
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
                            $response["err"] = "Có gì đó không ổn, vui lòng thử lại";
                        }
                    }
            }
            else{
                $response["err"] = "Sai tên đăng nhập hoặc mật khẩu";
                http_response_code(401);
            }
            $json_response = json_encode($response);
            echo $json_response;
        }

        public function Register(){
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $account_data = array(
                    "fullname" => $_POST['fullname'],
                    "email" => $_POST['email'],
                    "password" => $_POST['password'],
                    "retype_password" => $_POST['retype_password'],
                    "phone" => $_POST['phone'],
                    "address" => $_POST['address']
                );

                $account_data = array_map('trim', $account_data);
                
                // check xem email đã tồn tại chưa
                $model = $this->model("Customer");
                $customers = $model->FindCustomer($account_data['email']);
        
                if($customers == true){
                    $response["err"] = "Email đã tồn tại";
                    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                    echo $json_response;
                }
                else{
                       
                // hash mật khẩu
                    $pass_hash = hash('sha256', $account_data['password']);
                    $account_data['password'] = $pass_hash;

                    // tạo mã xác nhận
                    $verify_code = bin2hex(random_bytes(4));

                    // setup gửi mail kèm mã xác nhận
                    $data['email'] = $account_data['email'];
                    $data['fullname'] = $account_data['fullname'];
                    $data['subject'] = "Mã xác nhận cho SHOP PTIT";
                    $data['body'] = "Xin chào, " . $account_data['fullname'] ." <br> Bạn có đăng kí tài khoản tại ứng dụng của chúng tôi, đây là mã xác nhận của bạn:
                    <div style='font-size:20px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:10px;background-color:#f2f2f2;border-left:1px solid #ccc;border-right:1px solid #ccc;border-top:1px solid #ccc;border-bottom:1px solid #ccc'>" . $verify_code . "</div>. Lưu ý mã sẽ hết hiệu lục sau 5 phút!";
                    $res = $this->SendMail($data);

                    // nếu gửi thành công
                    if($res == 'sent'){

                        $key = getenv('key_api');
            
                    $payload = [
                        'iss' => 'http://localhost:8096',
                        'aud' => 'http://localhost:8096',
                        'iat' => time(),
                        'nbf' => time(),
                        'exp' => time() + (60 * 10),
                        'email' => $account_data['email'],
                        'full_name' => $account_data['fullname'],
                        'password' => $account_data['password'],
                        'phone' =>$account_data['phone'],
                        'verify_code' => $verify_code,
                        'address' =>$account_data['address'],
                        'count' => 0
                    ];
    
                    $jwt = JWT::encode($payload, $key, 'HS256');
                   
                            $response["token"] = $jwt;
                            $json_response = json_encode($response);
        
                            // Trả về dữ liệu JSON
                            echo $json_response;
                    }
                    else{
                        $response["err"] = "Lỗi khi gửi mã xác nhận, có thể do lỗi hệ thống hoặc email không đúng. Hãy kiểm tra và submit lại!";
                        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        
                        // Trả về dữ liệu JSON
                        echo $json_response;
                    }
                }

            }
        }

        function RandomPassword($length = 10) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
            return $randomString;
        }
        
        public function ForgotPassword(){
            $response = [];
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $verify_data = array(
                    "email" => $_POST['email']
                );

                $verify_data = array_map('trim', $verify_data);
                // check xem email đã tồn tại chưa
                $model = $this->model("Customer");
                $customers = $model->FindCustomer($verify_data['email']);

                if($customers == true){
                        // setup gửi mail kèm mã xác nhận
                        
                        $newPassword = $this->RandomPassword(16);
                        $data['email'] = $verify_data['email'];
                        $data['subject'] = "Reset mật khẩu tài khoản SHOP PTIT";
                        $data['body'] = "Bạn vừa gửi yêu cầu đặt lại mật khẩu vài phút trước, <br>Đây là mật khẩu mới của bạn <strong>(vui lòng đổi lại sau khi đăng nhập)</strong>:
                    <div style='font-size:20px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:10px;background-color:#f2f2f2;border-left:1px solid #ccc;border-right:1px solid #ccc;border-top:1px solid #ccc;border-bottom:1px solid #ccc'>" . $newPassword . "</div>.";
                        $res = $this->SendMail($data);

                                    // nếu gửi thành công
                        if($res == 'sent'){
                            $model = $this->model("Customer");
                            $dataResetPassword['email'] = $verify_data['email'];
                            $pass_hash = hash('sha256', $newPassword);
                            $dataResetPassword['password'] = $pass_hash;
                            $customers = $model->ResetPassword($dataResetPassword);

                            if($customers == 'done'){
                                $response['message'] = "Vui lòng kiểm tra mail để tiếp tục!";
                                $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        
                                // Trả về dữ liệu JSON
                                echo $json_response;
                            }
                            else{
                                $response['err'] = "Lỗi";
                                $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        
                                // Trả về dữ liệu JSON
                                echo $json_response;
                            }
                        }
                        else{
                            $response['err'] = "Lỗi khi gửi mail, có thể do lỗi hệ thống hoặc email không đúng. Hãy kiểm tra và submit lại!";
                            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
    
                            // Trả về dữ liệu JSON
                            echo $json_response;
                        }
                }
                else{
                    $response['message'] = "Vui lòng kiểm tra mail để tiếp tục!";
                    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);

                    // Trả về dữ liệu JSON
                    echo $json_response;
                }
            }
        }

        public function Logout($params){
			// Hủy tất cả các biến session
            $model = $this->model("Token");
			$res = $model->DeleteToken($params["account_info"]->email);
            if($res == "done"){
                $response["message"] = "Success";
                $json_response = json_encode($response);
                echo $json_response;
            }
            else{
                $response["err"] = "Có gì đó không ổn, vui lòng thử lại. " + $res;
            }
		}

        public function Verify(){
            $response = [];

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $data = array(
                    "verifyCode" => $_POST['verifyCode'],
                    "token" => $_POST['token']
                );

                $key = getenv('key_api');

                try {
                    // Giải mã token
                    $decoded = JWT::decode($data['token'], new Key($key, 'HS256'));
                        
                    if(time() > $decoded->exp){
                        $response['err'] = "Token hết hạn";
                        $json_response = json_encode($response);
                        echo $json_response;
                    }
                    else{
                        if($decoded->count < 3){
                            if($data['verifyCode'] == $decoded->verify_code){

                                // tạo mã giỏ hàng
                                $tmp =  explode("@",$decoded->email);
                                $cart_code = $tmp[0];
                                $cart_code = $cart_code . "_" . hash('sha256', $decoded->email);
        
                                $account_data = array(
                                    "full_name" => $decoded->full_name,
                                    "email" => $decoded->email,
                                    "password" => $decoded->password,
                                    "phone" =>  $decoded->phone,
                                    "cart_code" => $cart_code,
                                    "address" => $decoded->address
                                );

                                $model = $this->model("Customer");
                                $err = $model->InsertCustomer($account_data);
                                if($err == "done"){
                                    $response['message'] = "done";
                                }
                                else{
                                    $response['err'] =  $err;
                                }
                                $json_response = json_encode($response);
                                echo $json_response;
                            }
                            else{
                                $payload = [
                                    'iss' => $decoded->iss,
                                    'aud' => $decoded->aud,
                                    'iat' => $decoded->iat,
                                    'nbf' => $decoded->nbf,
                                    'exp' => $decoded->exp,
                                    'email' => $decoded->email,
                                    'full_name' => $decoded->full_name,
                                    'password' => $decoded->password,
                                    'verify_code' => $decoded->verify_code,
                                    'count' => $decoded->count + 1
                                ];
                
                                $jwt = JWT::encode($payload, $key, 'HS256');

                                $response['err'] = "Sai mã xác nhận";
                                $response['token'] = $jwt;

                                $json_response = json_encode($response);
                                echo $json_response;
                            }
                        }
                        else{
                            $response['err'] = "Đã nhập sai quá 3 lần";
                            $json_response = json_encode($response);
                            echo $json_response;
                        }
                        }             
                } catch (Exception $e) {
                    // Nếu có lỗi trong quá trình giải mã token, trả về lỗi
                    $response['err'] = "Lỗi khi giải mã token";
                    $json_response = json_encode($response);
                    echo $json_response;
                }
            }
		}
    }
?>