<?php 
    class Token extends DB{
        
        function checkTokenInDB($token){
                try {
                    $arr = [];
                    $db = new DB();
                    $sql = "select * from Tokens where token=?";
                    $params = array($token);
                    $sth = $db->select($sql, $params);
                    if ($sth->rowCount() > 0) {
                        return true;
                    }
                    return false;
                } catch (PDOException $e) {
                    $arr['err'] = "Lỗi";
                    return $arr;
                }
        }

        function checkUserHaveToken($username){
            try {
                $arr = [];
                $db = new DB();
                $sql = "select * from Tokens where username=?";
                $params = array($username);
                $sth = $db->select($sql, $params);
                if ($sth->rowCount() > 0) {
                    return true;
                }
                return false;
            } catch (PDOException $e) {
                $arr['err'] = "Lỗi";
                return $arr;
            }
    }

        function InsertToken($data){
            try {
                $arr = [];
                $db = new DB();
                $sql = "INSERT INTO `Tokens`(`token`, `username`) 
                VALUES (?,?)";
                $params = array($data['token'], $data['username']);
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

        function DeleteToken($username){
            try {
                $arr = [];
                $db = new DB();
                $sql = "DELETE FROM `Tokens` WHERE username = ?";
                $params = array($username);
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
    }
?>