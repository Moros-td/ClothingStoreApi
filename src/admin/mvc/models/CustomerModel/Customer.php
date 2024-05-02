<?php 
include_once "./mvc/models/CustomerModel/CustomerObj.php";
    class Customer extends DB{

        function checkAccount($data){
            try {
                $arr = [];
                $db = new DB();
                $sql = "SELECT C.*, SC.cart_code FROM Customers AS C, ShoppingCart AS SC 
                WHERE C.email = SC.email AND C.email = ? AND C.password = ?";
                $params = array($data['email'],$data['password']);
                $sth = $db->select($sql, $params);
                if ($sth->rowCount() > 0) {
                    $row = $sth->fetch();
                    $arr['cart_code'] = $row['cart_code'];
                    $arr['full_name'] = $row['full_name'];
                }
                return $arr;
            } catch (PDOException $e) {
                $a = [];
                return  $a;
            }
        }

        function checkPassword($data){
            try {
                $arr = [];
                $db = new DB();
                $sql = "SELECT C.* FROM Customers AS C
                 WHERE C.email = ? AND C.password = ?";
                $params = array($data['email'],$data['password']);
                $sth = $db->select($sql, $params);
                if ($sth->rowCount() > 0) {
                    return true;
                }
                return false;
            } catch (PDOException $e) {
                return  $e;
            }
        }

        function LoadCustomers(){
                try {
                    $db = new DB();
                    $sql = "SELECT C.full_name, C.phone, C.email 
                    FROM Customers AS C";
                    $sth = $db->select($sql);
                    $arr = [];
                    $customers_from_DB = $sth->fetchAll();
                    foreach ($customers_from_DB as $row) {

                        // tạo sản phẩm
                        $obj = new CustomerObj($row);
                        
                        // thêm obj vào mảng
                        $arr[] = $obj;
                    }
                    return $arr;
                } catch (PDOException $e) {
                    return  $sql . "<br>" . $e->getMessage();
                }
        }

        function FindCustomer($email){
            try {
                $db = new DB();
                $sql = "SELECT *
                FROM Customers AS C WHERE C.email = ?";
                $params = array($email);
                $sth = $db->select($sql, $params);

                if ($sth->rowCount() > 0) {
                    return true;
                }
                return false;
            } catch (PDOException $e) {
                return  $sql . "<br>" . $e->getMessage();
            }
        }

        function FindCustomerInfo($email){
            try {
                $obj = null;
                $db = new DB();
                $sql = "SELECT *
                FROM Customers AS C WHERE C.email = ?";
                $params = array($email);
                $sth = $db->select($sql, $params);
                if ($sth->rowCount() > 0) {
                    $row = $sth->fetch();
                    $obj = new CustomerObj($row);
                }
                return $obj;
            } catch (PDOException $e) {
                return  "Lỗi";
            }
        }

        function EditCustomer($data){
            try {
                $db = new DB();
                $sql = "UPDATE `Customers` SET `full_name` = ?, `phone` = ? WHERE `email` = ?;";
                $params = array($data['fullName'], $data['phone'], $data['email']);
                $db->execute($sql, $params);

                return "done";
            } catch (PDOException $e) {
                if($e->getCode() == '22001'){
                    return "Dữ liệu quá dài";
                }
                else{
                    return "Lỗi khi sửa thông tin khách hàng";
                    //return $e;
                }
                //echo  $sql . "<br>" . $e->getMessage();
            }
        }


        function InsertCustomerCart($db, $data){
            try {
                
                $sql = "INSERT INTO `ShoppingCart`(`cart_code`, `email`) 
                VALUES (?,?)";
                $params = array($data['cart_code'], $data['email']);
                $db->execute($sql, $params);

                return "done";
            } catch (PDOException $e) {
                throw $e;
                //echo  $sql . "<br>" . $e->getMessage();
            }
        }

        function InsertAddress($db, $data){
            try {
                $sql = "INSERT INTO `Addresses`(`address`, `email`, `is_default`) 
                VALUES (?,?,?)";
                $params = array($data['address'], $data['email'], true);
                $db->execute($sql, $params);

                return "done";
            } catch (PDOException $e) {
                throw $e;
            }
        }

        function InsertCustomer($data){
            try {
                $db = new DB();
                $db->conn->beginTransaction();
                $sql = "INSERT INTO `Customers`(`email`, `password`, `full_name`, `phone`) 
                VALUES (?,?,?,?)";
                $params = array($data['email'], $data['password'], $data['full_name'], $data['phone']);
                $db->execute($sql, $params);
                $this->InsertCustomerCart($db, $data);
                $this->InsertAddress($db, $data);
                $db->conn->commit();
                return "done";
            } catch (PDOException $e) {
                $db->conn->rollBack();
                //return "Lỗi khi thêm thông tin khách hàng";
                return $e->getMessage();
                //echo  $sql . "<br>" . $e->getMessage();
            }
        }


        function DeleteToken($data){
            try {
                $db = new DB();
                $sql = "DELETE FROM Verify AS V WHERE V.email = ?";
                $params = array($data['email']);
                $db->execute($sql, $params);
                return "done";
            } catch (PDOException $e) {
                return "Lỗi khi thông tin xác nhận";
                //echo  $sql . "<br>" . $e->getMessage();
            }
        }

        function UpdateToken($data){
            try {
                $db = new DB();
                $sql = "UPDATE Verify AS V SET V.token= ?,V.count= ?, V.used = ?, V.update_time = CURRENT_TIMESTAMP WHERE V.email = ? ";
                $params = array($data['token'], $data['count'], $data['used'], $data['email']);
                $db->execute($sql, $params);
                return "done";
            } catch (PDOException $e) {
                return "Lỗi khi thông tin xác nhận";
                //echo  $sql . "<br>" . $e->getMessage();
            }
        }

        function InsertToken($data){
            try {
                $db = new DB();
                $sql = "INSERT INTO `Verify`(`email`, `token`, `count`) VALUES (?,?,?)";
                $params = array($data['email'], $data['token'], $data['count']);
                $db->execute($sql, $params);
                return "done";
            } catch (PDOException $e) {
                return "Lỗi khi thông tin xác nhận";
                //echo  $sql . "<br>" . $e->getMessage();
            }
        }

        function FindCustomerVerify($data){
            try {
                $arr = [];
                $db = new DB();
                if(!empty($data['email'])){
                    $sql = "SELECT V.* FROM Verify AS V, Customers AS C 
                    WHERE V.email = C.email AND V.email = ?";
                    $params = array($data['email']);
                }
                else if(!empty($data['token'])){
                    $sql = "SELECT V.* FROM Verify AS V 
                    WHERE V.token = ?";
                    $params = array($data['token']);
                }
                $sth = $db->select($sql, $params);

                if ($sth->rowCount() > 0) {
                    $row = $sth->fetch();
                    $obj = new Verify($row);
                    $arr[] = $obj;
                }
                return $arr;
            } catch (PDOException $e) {

                $a = [];
                return $a;
                // return  $sql . "<br>" . $e->getMessage();
            }
        }

        
        function UpdateVerifyTokenStatus($data){
            try {
                $db = new DB();
                $sql = "UPDATE Verify AS V SET V.used=? WHERE V.email = ? ";
                $params = array($data['used'], $data['email']);
                $db->execute($sql, $params);
                return "done";
            } catch (PDOException $e) {
                return "Lỗi update trạng thái token";
                //echo  $sql . "<br>" . $e->getMessage();
            }
        }

        
        function UpdatePassword($data){
            try {
                $db = new DB();
                $sql = "UPDATE Customers AS C SET C.password= ? WHERE C.email = ?";
                $params = array($data['newPassword'], $data['email']);
                $db->execute($sql, $params);
                return "done";
            } catch (PDOException $e) {
                return "Lỗi khi update password";
                //echo  $sql . "<br>" . $e->getMessage();
            }
        }

        
    }
?>