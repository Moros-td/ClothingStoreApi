<?php
    class Controller{
        function model($model){
            
            if(file_exists("./mvc/models/". $model . "Model" ."/" . $model . ".php")){
                require_once "./mvc/models/". $model . "Model" ."/" . $model . ".php";
                return new $model;
            }
        }

        function view($view, $data){

            if(file_exists("./mvc/views/admin/" . $view . ".php")){
                require_once "./mvc/views/admin/" . $view. ".php";
            }
        }

        function validateNull($data){
            foreach($data as $key => $values){
                if(empty($values) || empty(trim($values))){
                    return true;
                }
            }
            return false;
        }

        function validateName($data){

            $pattern = '/^[\p{L}a-z A-Z]+$/u';

            foreach($data as $key => $values){
                if (!preg_match($pattern, $values))
                    return true;
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
                return true; // Đúng định dạng email
            } else {
                return false; // Không đúng định dạng email
            }
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