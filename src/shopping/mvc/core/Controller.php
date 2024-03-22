<?php
    class Controller extends Mail{
        function model($model){
            
            if(file_exists("./mvc/models/". $model . "Model" ."/" . $model . ".php")){
                require_once "./mvc/models/". $model . "Model" ."/" . $model . ".php";
                return new $model;
            }
        }

        function view($view, $data){
            if(file_exists("./mvc/views/shopping/" . $view . ".php")){
                require_once "./mvc/views/shopping/" . $view. ".php";
            }
        }

        function convert_vn2latin($str)
        {
            // Mảng các ký tự tiếng việt không dấu theo mã unicode dựng sẵn   
            $tv_unicode_dungsan  =
                [
                    "à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ","ặ","ẳ","ẵ",
                    "è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ",
                    "ì","í","ị","ỉ","ĩ",
                    "ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ","ờ","ớ","ợ","ở","ỡ",
                    "ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
                    "ỳ","ý","ỵ","ỷ","ỹ",
                    "đ",
                    "À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă","Ằ","Ắ","Ặ","Ẳ","Ẵ",
                    "È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
                    "Ì","Í","Ị","Ỉ","Ĩ",
                    "Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ","Ờ","Ớ","Ợ","Ở","Ỡ",
                    "Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
                    "Ỳ","Ý","Ỵ","Ỷ","Ỹ",
                    "Đ"
                ];
            // Mảng các ký không dấu sẽ thay thế cho ký tự có dấu
            $tv_khongdau =
                [
                    "a","a","a","a","a","a","a","a","a","a","a" ,"a","a","a","a","a","a",
                    "e","e","e","e","e","e","e","e","e","e","e",
                    "i","i","i","i","i",
                    "o","o","o","o","o","o","o","o","o","o","o","o" ,"o","o","o","o","o",
                    "u","u","u","u","u","u","u","u","u","u","u",
                    "y","y","y","y","y",
                    "d",
                    "A","A","A","A","A","A","A","A","A","A","A","A" ,"A","A","A","A","A",
                    "E","E","E","E","E","E","E","E","E","E","E",
                    "I","I","I","I","I",
                    "O","O","O","O","O","O","O","O","O","O","O","O" ,"O","O","O","O","O",
                    "U","U","U","U","U","U","U","U","U","U","U",
                    "Y","Y","Y","Y","Y",
                    "D"
                ];

            $str = str_replace($tv_unicode_dungsan, $tv_khongdau, $str);
            return $str;
        }

        function Str2Url($str)
        {
            // Chuyển tiếng việt không dấu
            $str = $this->convert_vn2latin($str);
            // chuyển sang in thường
            $str = mb_strtolower($str);
            // Giữ lại các ký tự chữ a - z và số 0 - 9 còn lại thay bằng -
            $str = preg_replace('/[^a-z0-9]/', '-', ($str));
            $str = preg_replace('/[--]+/', '-', $str);
            $str = trim($str, '-');
            return $str;
        }

        function validateNull($data){
            foreach($data as $key => $values){
                if(empty($values) || empty(trim($values))){
                    return true;
                }
            }
            return false;
        }

        function validateSpecialCharacter($data){

            $pattern = '/^[\p{L}a-z A-Z0-9]+$/u';

            foreach($data as $key => $values){
                if (!preg_match($pattern, $values))
                    return true;
            }
            return false;
        }

        function validateNumber($data){
            foreach($data as $key => $values){
                if (!is_numeric($values) || $values < 0) {
                    return true;
                }
            }
            return false;
        }

        function validateEmail($email) {
            // Sử dụng hàm filter_var với FILTER_VALIDATE_EMAIL để kiểm tra tính hợp lệ của email
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if(strlen($email) > 100)
                    return false;
                return true; // Đúng định dạng email
            } else {
                return false; // Không đúng định dạng email
            }
        }

        function validateUsername($username){
                // Kiểm tra username có ít nhất 10 ký tự
            if (strlen($username) < 10) {
                return "Username yêu cầu tối thiểu 10 kí tự";
            }

            if (strlen($username) > 50) {
                return "Username quá dài";
            }

            // Kiểm tra username có chứa kí tự không phải chữ hoặc số không
            if (!preg_match('/^[a-z0-9]+$/', $username)) {
                return "Username chỉ chứa chữ thường và số";
            }
            
            // Kiểm tra username có chứa ít nhất một kí tự chữ và một số
            if (!preg_match('/[a-zA-Z]/', $username) || !preg_match('/[0-9]/', $username)) {
                return "Username hợp lệ: moros12345";
            }
            
            // Kiểm tra username có bắt đầu bằng chữ cái
            if (!ctype_alpha($username[0])) {
                return "Username hợp lệ: moros12345";
            }
            
            return "validated";

        }

        function validFullName($fullName) {
            // Kiểm tra xem tên có chứa ký tự đặc biệt hoặc số không
            if (!preg_match('/^[\p{L}a-zA-Z ]+$/u', $fullName)) {
                return "Tên không được chứa kí tự đặc biệt hoặc số";
            }

            if (strlen($fullName) > 100) {
                return "Tên quá dài";
            }
            
            return "validated";
        }

        function checkStrongPassword($data) {
            if($data['password'] != $data['retype_password']){
                return "Vui lòng nhập cùng một mật khẩu!";
            }

            if(strlen($data['password']) > 100){
                return "Mật khẩu quá dài!";
            }

            if(strlen($data['password']) < 10){
                return "Mật khẩu tối thiểu phải 10 kí tự!";
            }

            $uppercase = preg_match('@[A-Z]@', $data['password']); // Ít nhất một ký tự hoa
            $lowercase = preg_match('@[a-z]@', $data['password']); // Ít nhất một ký tự thường
            $number    = preg_match('@[0-9]@', $data['password']); // Ít nhất một số
            $specialChars = preg_match('@[^\w]@', $data['password']); // Ít nhất một ký tự đặc biệt

            if(!$uppercase){
                return "Mật khẩu cần tối thiểu 1 kí tự hoa";
            }
            if(!$lowercase){
                return "Mật khẩu cần tối thiểu 1 kí tự thường";
            }
            if(!$number){
                return "Mật khẩu cần tối thiểu 1 kí tự số";
            }
            if(!$specialChars){
                return "Mật khẩu cần tối thiểu 1 kí tự đặc biệt";
            }
            
            return "validated";
        }
    }
?>