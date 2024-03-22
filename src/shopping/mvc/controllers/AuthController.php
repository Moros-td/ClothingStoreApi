<?php
use Firebase\JWT\JWT;

    class AuthController extends Controller{
        private $categories;
        private $access = false;

        function CheckAccess(){
            if($this->access == false){
                header('Location: /Dashboard_category');
                exit;
            }
        }

        public function __construct()
        {   
            // load category
            // $model = $this->model("Category");
            // $data_category = $model->LoadCategories();

            // foreach($data_category as $each){
            //     $key = $this->Str2Url($each->getName());
            //     $parent_name = $this->Str2Url($each->getParent_category_name());
            //     $each->setParent_category_name($parent_name);
            //     $data[$key] = $each;
            // }

            // $this->categories = $data;
        }
        
        function Login(){

            // lấy và validate data
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
                            $response["err"] = "Something wrong";
                        }
                    }
            }
            else{
                $response["err"] = "Wrong email or password";
                http_response_code(401);
            }
            $json_response = json_encode($response);
            echo $json_response;
        }

        public function validateAccount($data){
            $this->CheckAccess();

            if($this->validateNull($data)){
                return "Vui lòng nhập đủ thông tin";
            }

            $arr_Number['phone'] = $data['phone'];

            $err = $this->validFullName($data['fullname']);
            
            if($err != "validated"){
                return $err;
            }
            
            if($this->validateNumber($arr_Number)){
                //var_dump($arr_Number);
                return "Số điện thoại không hợp lệ";
            }

            if($data['phone'][0] != '0' || strlen($data['phone']) < 10){
                return "Số điện thoại không hợp lệ";
            }

            if (!$this->validateEmail($data['email'])) {
                return "Email không hợp lệ!";
            }

            $password['password'] = $data['password'];
            $password['retype_password'] = $data['retype_password'];

            $err = $this->checkStrongPassword($password);
            if($err != "validated"){
                return $err;
            }
            return "validated";
        }

        public function Register(){
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $account_data = array(
                    "fullname" => $_POST['fullname'],
                    "email" => $_POST['email'],
                    "password" => $_POST['password'],
                    "retype_password" => $_POST['retype_password'],
                    "phone" => $_POST['phone']
                );
                $this->access = true;
                $account_data = array_map('trim', $account_data);
                $err = $this->validateAccount($account_data);
                
                if($err == "validated"){
                    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
                    {
                        $secret = '6Ld6ijcpAAAAACKaMc5Aj023Y2LGsN2rqPZSHcpu'; //Thay thế bằng mã Secret Key của bạn
                        $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
                        $response_data = json_decode($verify_response);
                        if($response_data->success)
                        {
                                                // check xem email đã tồn tại chưa
                            $model = $this->model("Customer");
                            $customers = $model->FindCustomer($account_data['email']);
        
                            if($customers == true){
                                echo "Email đã tồn tại";
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
                                $data['body'] = "Xin chào, " . $account_data['fullname'] ." <br> Bạn có đăng kí tài khoản tại trang web của chúng tôi, đây là mã xác nhận của bạn:
                                <div style='font-size:20px;font-family:LucidaGrande,tahoma,verdana,arial,sans-serif;padding:10px;background-color:#f2f2f2;border-left:1px solid #ccc;border-right:1px solid #ccc;border-top:1px solid #ccc;border-bottom:1px solid #ccc'>" . $verify_code . "</div>. Lưu ý mã sẽ hết hiệu lục sau 5 phút!";
                                $res = $this->SendMail($data);

                                // nếu gửi thành công
                                if($res == 'sent'){

                                    // tạo session về thông tin khách hàng cũng như mã xác nhận
                                    $_SESSION['account_data'] =  $account_data;
                                    $token = bin2hex(random_bytes(20));
                                    $_SESSION['token'] = $token;
                                    $_SESSION['create_time'] = time();
                                    $_SESSION['count'] = 0;
                                    $_SESSION['verify_code'] = $verify_code;
                                    echo "token:" . $token;

                                }
                                else{
                                    echo "Lỗi khi gửi mã xác nhận, có thể do lỗi hệ thống hoặc email không đúng. Hãy kiểm tra và submit lại!";
                                }
                            }
                        }
                        else
                        {
                            echo 'Không thể xác thực, vui lòng thử lại';
                        }
                    }
                    else{
                        echo 'Không thể xác thực, vui lòng thử lại';
                    }
                }
                else{
                    echo $err;
                }
            }
        }

        // chưa xong
        public function ForgotPassword(){
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $check = true;
                $verify_data = array(
                    "email" => $_POST['email']
                );
                $this->access = true;
                $verify_data = array_map('trim', $verify_data);
                if (!$this->validateEmail($verify_data['email']) || empty($verify_data['email'])) {
                    echo "Email không hợp lệ!";
                }
                else{
                    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
                    {
                        $secret = '6Ld6ijcpAAAAACKaMc5Aj023Y2LGsN2rqPZSHcpu'; //Thay thế bằng mã Secret Key của bạn
                        $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
                        $response_data = json_decode($verify_response);
                        if($response_data->success)
                        {
                            // check xem email đã tồn tại chưa
                            $model = $this->model("Customer");
                            $customers = $model->FindCustomer($verify_data['email']);

                            if($customers == true){

                                $arrVerify = $model->FindCustomerVerify($verify_data);

                                // tạo mã xác nhận
                                $token = bin2hex(random_bytes(100));
                                // nếu chưa gửi mã xác thực lần nào
                                if(empty($arrVerify)){
                                    $verify_data['token'] = $token;
                                    $verify_data['count'] = 0;
                                    $err = $model->InsertToken($verify_data);
                                    if($err != 'done'){
                                        $check = false;
                                        echo "Lỗi";
                                    }
                                }else{
                                    $oneDayInSeconds = 24 * 60 * 60; // Số giây trong một ngày

                                    if($arrVerify[0]->getCount() > 3){
                                        $dateTime = new DateTime($arrVerify[0]->getCreate_time());
                                        $timestamp = $dateTime->getTimestamp();
                                        if (time() - $timestamp >= $oneDayInSeconds) {
                                            $verify_data['token'] = $token;
                                            $verify_data['count'] = 0;
                                            $err = $model->DeleteToken($verify_data);
                                            if($err != 'done'){
                                                $check = false;
                                                echo "Lỗi";
                                            }
                                            else{
                                                $err = $model->InsertToken($verify_data);
                                                if($err != 'done'){
                                                    $check = false;
                                                    echo "Lỗi";
                                                }
                                            }
                                        } else{
                                            $check = false;
                                            echo "Bạn đã quá số lần gửi mã xác nhận cho hôm nay! Thử lại sau 24 tiếng!";
                                        }
                                    }
                                    else{
                                        //echo $arrVerify[0]->getCreate_time();
                                        $dateTime = new DateTime($arrVerify[0]->getCreate_time());
                                        $timestamp = $dateTime->getTimestamp();
                                        if (time() - $timestamp >= $oneDayInSeconds) {
                                            $verify_data['token'] = $token;
                                            $verify_data['count'] = 0;
                                            $err = $model->DeleteToken($verify_data);
                                            if($err != 'done'){
                                                $check = false;
                                                echo "Lỗi";
                                            }
                                            else{
                                                $err = $model->InsertToken($verify_data);
                                                if($err != 'done'){
                                                    $check = false;
                                                    echo "Lỗi";
                                                }
                                            }
                                        } else {
                                            $verify_data['token'] = $token;
                                            $verify_data['count'] = $arrVerify[0]->getCount() + 1;
                                            $verify_data['used'] = 0;
                                            $err = $model->UpdateToken($verify_data);
                                            if($err != 'done'){
                                                $check = false;
                                                echo "Lỗi";
                                            }
                                        }
                                    }
                                }
                                if($check){
                                    // setup gửi mail kèm mã xác nhận
                                    //$link = 'localhost:8092/Auth/RestPassword/' . $token;
                                    $link = "<a href='http://localhost:8092/Auth/ResetPassword/$token'>Bấm vào đây để đặt lại mật khẩu</a>";
                                    $data['email'] = $verify_data['email'];
                                    $data['subject'] = "Reset mật khẩu tài khoản SHOP PTIT";
                                    $data['body'] = '<html><body>Bạn vừa gửi yêu cầu đặt lại mật khẩu vài phút trước:<br> ' . $link . '</body></html>';
                                    $res = $this->SendMail($data);

                                    // nếu gửi thành công
                                    if($res == 'sent'){
                                        echo "Vui lòng kiểm tra mail của bạn để tiếp tục!";
                                    }
                                    else{
                                        echo "Lỗi khi gửi mail, có thể do lỗi hệ thống hoặc email không đúng. Hãy kiểm tra và submit lại!";
                                    }
                                }
                            }
                            else{
                                echo "Vui lòng kiểm tra mail của bạn để tiếp tục!";
                            }
                        }
                        else
                        {
                            echo 'Không thể xác thực, vui lòng thử lại';
                        }
                    }
                    else{
                        echo 'Không thể xác thực, vui lòng thử lại';
                    }
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
                $response["err"] = "Something wrong";
            }
		}

        public function Verify($params){
            // đổi id session mỗi lần truy cập
            session_regenerate_id(true);

            $tmp = [];
            foreach($this->categories as $key => $value){
                $tmp[$value->getParent_category_name()][$key] =  $value->getName();
            }
            
            $data['categories'] = $tmp;

            //check nếu params chỉ có 1 phần tử mới được chạy tiếp
            // so sánh token với chuỗi trong db
            // nếu giống thì báo thành công và hiện nút đăng nhập cho user
            // nếu không báo quay về trang chủ
            // xóa token trong db
            // nếu dược thì chỉ cho token tồn tại 15'

            $timeout = 300;

            if($params[0] == $_SESSION['token']){
                if(time() - $_SESSION['create_time'] > 300){
                    session_unset();
                    $page = $this->view("404", $data);
                }
                else{
                    if(isset($_POST['confirmCode'])){
                        if($_SESSION['count'] < 3){
                            if($_POST['confirmCode'] == $_SESSION['verify_code']){
                                // tạo mã giỏ hàng
                                $tmp =  explode("@",$_SESSION['account_data']['email']);
                                $cart_code = $tmp[0];
                                $cart_code = $cart_code . "_" . hash('sha256', $_SESSION['account_data']['email']);

                                $account_data = array(
                                    "full_name" => $_SESSION['account_data']['fullname'],
                                    "email" => $_SESSION['account_data']['email'],
                                    "password" => $_SESSION['account_data']['password'],
                                    "phone" => $_SESSION['account_data']['phone'],
                                    "cart_code" => $cart_code
                                );
                                
                                session_unset();
                                                    // check xem email đã tồn tại chưa
                                $model = $this->model("Customer");
                                $customers = $model->FindCustomer($_SESSION['email']);

                                if($customers == true){
                                    $_SESSION['create_time'] -= 300;
                                    $_SESSION['err'] =  "Email đã tồn tại vui lòng đăng kí lại!";
                                    $page = $this->view("confirmUser", $data);
                                }
                                else{

                                    $model = $this->model("Customer");
                                    $err = $model->InsertCustomer($account_data);
                                    if($err == "done"){
                                        $page = $this->view("registerSuccess", $data);
                                    }
                                    else{
                                        $_SESSION['err'] =  $err;
                                        $page = $this->view("confirmUser", $data);
                                    }
                                }
                            }
                            else{
                                $_SESSION['err'] = "Sai mã nhận";
                                $_SESSION['count'] += 1;
                                $page = $this->view("confirmUser", $data);
                            }
                        }
                        else{
                            $_SESSION['create_time'] -= 300;
                            $_SESSION['err'] =  "Quá số lần nhập, vui lòng tạo lại tài khoản";
                            $page = $this->view("confirmUser", $data);
                        }
                    }
                    else{
                        $page = $this->view("confirmUser", $data);
                    }
                }
            }               
            else{
                header("Location: /Auth");
            }
		}

        public function ResetPassword($params){

            $tmp = [];
            foreach($this->categories as $key => $value){
                $tmp[$value->getParent_category_name()][$key] =  $value->getName();
            }
            
            $data['categories'] = $tmp;
            if(!empty($params)){

                $data_token['token'] = $params[0];
                $model = $this->model("Customer");
                $arrVerify = $model->FindCustomerVerify($data_token);

                if(empty($arrVerify)){
                    header("Location: /Auth");
                }
                else{
                    $dateTime = new DateTime($arrVerify[0]->getUpdate_time());
                    $update_time = $dateTime->getTimestamp();
                    if(time() - $update_time  > 300 || $arrVerify[0]->getUsed() == 1){
                        if($arrVerify[0]->getUsed() == 0){
                            $data_user = [
                                'email' => $arrVerify[0]->getEmail(),
                                'used' => 1
                            ];
                            $err = $model->UpdateVerifyTokenStatus($data_user);
                        }
                        $page = $this->view("404", $data);
                    }
                    else{
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            if(empty($_POST['password']) || empty($_POST['retype_password'])){
                                echo "Vui lòng nhập đủ thông tin";
                            }
                            else{
                                    $data_pass['password'] = $_POST['password'];
                                    $data_pass['retype_password'] = $_POST['retype_password'];
                                    $err = $this->checkStrongPassword($data_pass);
                                    if($err != "validated"){
                                        return $err;
                                    }
                                    else{
                                        $password = hash('sha256', $_POST['password']);
                                        $data_user = [
                                            'email' => $arrVerify[0]->getEmail(),
                                            'password' => $password,
                                            'used' => 1
                                        ];
    
                                        $model = $this->model("Customer");
                                        $err = $model->ResetPassword($data_user);
                                        if($err != "done"){
                                            echo $err;
                                        }
                                        else{
                                            $err = $model->UpdateVerifyTokenStatus($data_user);
                                            if($err != "done"){
                                                echo $err;
                                            }
                                            else{
                                                echo  "done";
                                            }
                                        }
                                    }
                            }
                        }
                        else{
                            $data['token'] = $params[0];
                            $page = $this->view("changePassword", $data);
                        }
                    }   
                }
            }              
            else{
                header("Location: /Auth");
            }
		}
    }
?>