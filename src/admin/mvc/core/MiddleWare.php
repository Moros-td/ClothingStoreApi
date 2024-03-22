<?php
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
    class MiddleWare{
        public function __construct() {}

        public function checkAuth() {
            $headers = getallheaders();
            // Kiểm tra xem có header 'Authorization' được gửi từ client không
            if (!array_key_exists('Authorization', $headers)) {

                return "Authorization header is missing";
            }

            // Lấy giá trị token từ header 'Authorization'
            if (substr($headers['Authorization'], 0, 7) !== 'Bearer ') {

                return "Bearer keyword is missing";
            }

            $jwt = trim(substr($headers['Authorization'], 6));

            $res = $this->checkTokenInDB($jwt);
            if($res){
                $key = getenv('key_api');
                try {
                    // Giải mã token
                    $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
                    // Kiểm tra các thông tin cần thiết từ payload
                    // ở đây bạn có thể kiểm tra thêm các thông tin khác như role, expire time...
                    // Nếu thông tin không hợp lệ, trả về lỗi
                    if (!isset($decoded->iss) || $decoded->iss !== 'http://localhost:8097' || !isset($decoded->aud) || $decoded->aud !== 'http://localhost:8097') {
                        return "Invalid access";
                    }
    
                    if(!isset($decoded->exp) || $decoded->exp <= time()){
                        return "Token is expired";
                    }
    
                    // Nếu mọi thông tin đều hợp lệ, trả về phản hồi OK
                    return $decoded;
                } catch (Exception $e) {
                    // Nếu có lỗi trong quá trình giải mã token, trả về lỗi
                    return "Unauthenticated";
                }
            }
            else{
                return "Unauthenticated";
            }
        }

        public function checkTokenInDB($jwt){
            require_once "./mvc/models/TokenModel/Token.php";
            $model = new Token();
            $result = $model->checkTokenInDB($jwt);
            if($result && is_bool($result))
                return true;
            else
                return false;
        }
    }
?>