<?php 
include_once "./mvc/models/CategoryModel/CategoryObj.php";
    class Category extends MiddleWare{

        function LoadCategories(){
                try {
                    $db = new DB();
                    $sql = "SELECT C1.*, C2.name AS 'parent_name' FROM Categories AS C1 
                    LEFT JOIN Categories AS C2 ON C1.parent_category_id = C2.category_id";
                    $sth = $db->select($sql);
                    $arr = [];
                    $categories_from_DB = $sth->fetchAll();
                    foreach ($categories_from_DB as $row) {

                        // tạo sản phẩm
                        $obj = new CategoryObj($row);
                        $category_parent['category_id'] = $row['parent_category_id'];
                        $category_parent['name'] = $row['parent_name'];
                        $obj_category_parent = new CategoryObj($category_parent);

                        $obj->setParent_category($obj_category_parent);
                        // thêm obj vào mảng
                        $arr[] = $obj;
                    }
                    return $arr;
                } catch (PDOException $e) {
                    return  $sql . "<br>" . $e->getMessage();
                }
        }

        function InsertCategory($data){
            try {
                $db = new DB();
                $sql = "INSERT INTO `Categories`(`name`, `parent_category_id`) VALUES (?, ?);";
                $params = array($data['category_name'], $data['category_parent_id']);
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
                    //echo "Lỗi: " . $e->getMessage();
                    return "Lỗi khi thêm danh mục";
                }
            }
        }

        function EditCategory($data){
            try {
                $db = new DB();
                $sql = "UPDATE `Categories` SET `name` = ?, `parent_category_id` = ? WHERE `category_id` = ?;";
                $params = array($data['category_name'], $data['category_parent_id'], $data['category_id']);
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
                    return "Lỗi: " . $e->getMessage();
                    //return "Lỗi khi sửa danh mục";
                }
            }
        }

        function DeleteCategory($data){
            try {
                $db = new DB();
                $sql = "DELETE FROM `Categories` WHERE `category_id` = ?;";
                $params = array($data['category_id']);
                $db->execute($sql, $params);

                echo "done";
            } catch (PDOException $e) {

                //echo "Tồn tại sản phẩm thuộc danh mục này, không thể xóa";
                if ($e->getCode() == '42000') {
                    // Xử lý khi có lỗi SQLSTATE 42000
                    return "Bạn không có quyền làm thao tác này";
                } else if($e->getCode() == '23000'){
                    // Xử lý cho các lỗi khác
                    //return "Lỗi: " . $e->getMessage();
                    return "Danh mục hiện có sản phẩm tham chiếu, không thể xóa";
                }
                else{
                    return "Lỗi khi xóa danh mục";
                }
            }
        }
    }
?>