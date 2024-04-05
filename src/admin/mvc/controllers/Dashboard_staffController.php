<?php
    class Dashboard_staffController extends Controller{

        function getAllStaffs(){
            $model = $this->model("Admin");
            $data = $model->LoadAdmins();
            
            $json_response = json_encode($data, JSON_UNESCAPED_UNICODE);
            echo $json_response;
        }

        function validationStaff($data){
            // check thiếu data

            if($this->validateNull($data)){
                if(empty($data["csrf_token_staff"])){
                    return "Lỗi";
                }
                echo "Vui lòng nhập đủ thông tin";
                return;
            }

            $arr_Str["username"] = $data['username'];

            if($this->validateSpecialCharacter($arr_Str)){
                return "Dữ liệu không được chứa kí tự đặc biệt";
            }

            return "validated";
        }

        function AddStaff(){
            $response = [];
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
                $staff_data = array(
                    "username" => $_POST['username'],
                    "password" => $_POST['password'],
                    "role" => $_POST['role']
                );
                $staff_data = array_map('trim', $staff_data);
                
                if($staff_data['role'] == 'admin'){
                    $response["err"] = "Không được thêm admin";
                    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                    echo $json_response;
                    return;
                }
   
                $staff_data['password'] = hash('sha256', $staff_data['password']);
                $model = $this->model("Admin");
                $err = $model->AddStaff($staff_data);

                if($err != "done"){
                    $response["err"] = $err;
                } else{
                    $response["message"] = "Thêm nhân viên thành công!";
                }
                $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                echo $json_response;
            }
        }

        function EditStaff(){
            $response = [];
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
                $staff_data = array(
                    "username" => $_POST['username'],
                    "role" => $_POST['role']
                );
                $staff_data = array_map('trim', $staff_data);
                    
            
                if($staff_data['role'] == 'admin'){
                    $response['err'] = "Không được sửa nhân viên thành admin";
                    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                    echo $json_response;
                    return;
                }
                $model = $this->model("Admin");

                $user = $model->FindAdmin($staff_data['username']);

                if($user->getRole() == 'admin'){
                    $response['err'] = "Không có quyền sửa admin";
                    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                    echo $json_response;
                }
                else{
                    if(empty($user->getUsername())){
                        $response['err'] = "User không tồn tại!";
                        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                        echo $json_response;
                    }
                    else{
                        $err = $model->EditStaff($staff_data);
                        if($err != "done"){
                            $response['err'] = $err;
                        }
                        else{
                            $response['message'] = "Sửa thông tin thành công!";
                        }
                        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                        echo $json_response;
                    }
                }
            }
        }

        function ResetPassword(){
            $response = [];
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
                $staff_data = array(
                    "username" => $_POST['username'],
                    "password" => $_POST['password']
                );
                $staff_data = array_map('trim', $staff_data);
                $model = $this->model("Admin");

                $user = $model->FindAdmin($staff_data['username']);

                if($user->getRole() == 'admin'){
                    $response['err'] = "Không có quyền reset password admin";
                    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                    echo $json_response;
                }
                else{
                    if(empty($user->getUsername())){
                        $response['err'] = "User không tồn tại!";
                        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                        echo $json_response;
                    }
                    else{
                        $data_password['password'] = $staff_data['password'];
                        $data_password['retype_password'] = $staff_data['password'];
 
                        $staff_data['password'] = hash('sha256', $staff_data['password']);
                        $err = $model->ResetPassword($staff_data);
                        if($err != "done"){
                            $response['err'] = $err;
                        }
                        else{
                            $response['message'] = "Đổi mật khẩu thành công!";
                        }
                        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                        echo $json_response;
                    }
                }

            }
        }

        function DeleteStaff(){
            $response = [];
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
                $staff_data = array(
                    "username" => $_POST['username']
                );
                $staff_data = array_map('trim', $staff_data);

                $model = $this->model("Admin");

                $user = $model->FindAdmin($staff_data['username']);

                if($user->getRole() == 'admin'){
                    $response['err'] = "Không có quyền xóa admin";
                    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                    echo $json_response;
                }
                else{
                    if(empty($user->getUsername())){
                        $response['err'] = "User không tồn tại!";
                        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                        echo $json_response;
                    }
                    else{
                        $err = $model->DeleteStaff($staff_data);
                        if($err != "done"){
                            $response['err'] = $err;
                        }
                        else{
                            $response['message'] = "Xóa nhân viên thành công!";
                        }
                        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                        echo $json_response;
                    }
                }
            }
        }
    }
?>