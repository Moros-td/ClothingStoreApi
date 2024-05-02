<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AddressController extends Controller
{
    function LoadAllAddress(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];
            $model = $this->model("Address");
            $result = $model->LoadAllAddress($email);
            echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }
    function SetAddressDefault(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'email' => $_POST['email'],
                'address_id'=>$_POST['address_id']
            ];
            $model = $this->model("Address");
            $result = $model->SetAddressDefault($data);
            if ($result != "done") {
                echo json_encode(['error' => $result], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo json_encode(['success' => 'done'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }    

    function DeleteAddress(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'email' => $_POST['email'],
                'address_id'=>$_POST['address_id']
            ];
            $model = $this->model("Address");
            $result = $model->DeleteAddress($data);
            if ($result != "done") {
                echo json_encode(['error' => $result], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo json_encode(['success' => 'done'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }    

    function AddAddress(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'email' => $_POST['email'],
                'address'=>$_POST['address']
            ];
            $model = $this->model("Address");
            $result = $model->AddAddress($data);
            if ($result != "done") {
                echo json_encode(['error' => $result], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo json_encode(['success' => 'done'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }   

    function UpdateAddress(){
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'email' => $_POST['email'],
                'address'=>$_POST['address'],
                'addressId'=> $_POST['addressId']
            ];
            $model = $this->model("Address");
            $result = $model->UpdateAddress($data);
            if ($result != "done") {
                echo json_encode(['error' => $result], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo json_encode(['success' => 'done'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }   
}
?>