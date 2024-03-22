<?php 
include_once "./mvc/models/CategoryModel/CategoryObj.php";
    class Category extends DB{

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
                        
                        // thêm obj vào mảng
                        $arr[] = $obj;
                    }
                    return $arr;
                } catch (PDOException $e) {
                    return  $sql . "<br>" . $e->getMessage();
                }
        }
    }
?>