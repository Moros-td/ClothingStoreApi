<?php 
include_once "./mvc/models/AdminModel/AdminObj.php";
    class Admin extends DB{
        
        function checkAccount($data){
                try {
                    $arr = [];
                    $db = new DB();
                    $sql = "select * from AdminAccounts where username=? and password=?";
                    $params = array($data["username"],$data["password"]);
                    $sth = $db->select($sql, $params);
                    if ($sth->rowCount() > 0) {
                        $row = $sth->fetch();
                        $arr['role'] = $row['role'];
                    }
                    return $arr;
                } catch (PDOException $e) {
                    $arr['err'] = "Lỗi";
                    return $arr;
                }
        }

        function LoadAdmins(){
            try {
                $arr = [];
                $db = new DB();
                $sql = "select * from AdminAccounts";
                $sth = $db->select($sql);
                $admin_from_DB = $sth->fetchAll();
                foreach ($admin_from_DB as $row) {
                    // thêm obj vào mảng
                    
                    $obj = new AdminObj($row);

                    $arr[] = $obj;
                }
                return $arr;
            } catch (PDOException $e) {
                return  $sql . "<br>" . $e->getMessage();
            }
        }

        function FindAdmin($username){
            try {
                $db = new DB();
                $sql = "select * from AdminAccounts WHERE username = ?";
                $params = array($username);
                $sth = $db->select($sql, $params);
                $admin_from_DB = $sth->fetchAll();
                foreach ($admin_from_DB as $row) {
                    // thêm obj vào mảng
                    
                    $obj = new AdminObj($row);

                }
                return $obj;
            } catch (PDOException $e) {
                // return  $sql . "<br>" . $e->getMessage();
                return "Lỗi";
            }
        }

        function AddStaff($data){
            try {
                $arr = [];
                $db = new DB();
                $sql = "INSERT INTO `AdminAccounts`(`username`, `password`, `role`) 
                VALUES (?,?,?)";
                $params = array($data['username'], $data['password'], $data['role']);
                $db->execute($sql, $params);
                
                return "done";
            } catch (PDOException $e) {
                if ($e->getCode() == '42000') {
                    // Xử lý khi có lỗi SQLSTATE 42000
                    return "Bạn không có quyền làm thao tác này";
                }
                else if ($e->getCode() == '23000') {
                    // Lỗi ràng buộc duy nhất
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        // Xử lý trường hợp giá trị bị trùng lặp
                        return "Đã tồn tại username này";
                    }
                } 
                else {
                    if($e->getCode() == '22001'){
                        return "Dữ liệu quá dài";
                    }
                    return "Lỗi";
                    //return "Lỗi khi thêm staff";
                }
            }
        }

        function EditStaff($data){
            try {
                $arr = [];
                $db = new DB();
                $sql = "UPDATE `AdminAccounts` SET `role` = ? WHERE username = ?";
                $params = array($data['role'], $data['username']);
                $db->execute($sql, $params);
                
                return "done";
            } catch (PDOException $e) {
                if ($e->getCode() == '42000') {
                    // Xử lý khi có lỗi SQLSTATE 42000
                    return "Bạn không có quyền làm thao tác này";
                } else {
                    if($e->getCode() == '22001'){
                        return "Dữ liệu quá dài";
                    }
                    return "Lỗi khi sửa thông tin staff";
                    //return "Lỗi khi thêm staff";
                }
            }
        }

        function DeleteStaff($data){
            try {
                $arr = [];
                $db = new DB();
                $sql = "DELETE FROM `AdminAccounts` WHERE username = ?";
                $params = array($data['username']);
                $db->execute($sql, $params);
                
                return "done";
            } catch (PDOException $e) {
                if ($e->getCode() == '42000') {
                    // Xử lý khi có lỗi SQLSTATE 42000
                    return "Bạn không có quyền làm thao tác này";
                } else {
                    // Xử lý cho các lỗi khác
                    //return "Lỗi: " . $e->getMessage();
                    return "Lỗi khi xóa";
                }
            }
        }

        function EditStatus($data){
            try {
                $arr = [];
                $db = new DB();
                $sql = "CALL ResetStatus(?)";
                $params = array($data[0]);
                $db->execute($sql, $params);
                
                return "done";
            } catch (PDOException $e) {
                //return "Lỗi khi sửa trạng thái";
                return "Lỗi";
            }
        }

        function ResetPassword($data){
            try {
                $arr = [];
                $db = new DB();
                $sql = "UPDATE `AdminAccounts` SET `password` = ? WHERE username = ?";
                $params = array($data['password'], $data['username']);
                $db->execute($sql, $params);
                
                return "done";
            } catch (PDOException $e) {
                if ($e->getCode() == '42000') {
                    // Xử lý khi có lỗi SQLSTATE 42000
                    return "Bạn không có quyền làm thao tác này";
                } else {
                    if($e->getCode() == '22001'){
                        return "Dữ liệu quá dài";
                    }
                    return "Lỗi";
                    //return "Lỗi khi reset password";
                }
            }
        }
    }
?>