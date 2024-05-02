<?php 
include_once "./mvc/models/ProductModel/ProductObj.php";
    class Product extends DB{

        function LoadProductSizes($product_code){
            try {
                    $db = new DB();
                    $sql = "CALL GetSizesProduct(?)";
                    $params = array($product_code);
                    $sth = $db->select($sql, $params);
                    $sizes = [];
                    while($row = $sth->fetch()) {
                        $sizes[$row['size']] = $row['quantity'];
                    }

                return $sizes;
            } catch (PDOException $e) {
                return  $sql . "<br>" . $e->getMessage();
            }
        }

        function LoadProductImages($product_code){
            try {
                    $db = new DB();
                    $sql = "CALL GetImagesProduct(?)";
                    $params = array($product_code);
                    $sth = $db->select($sql, $params);
                    $images = [];
                    while($row2 = $sth->fetch()) {
                        $images[] = $row2['image'];
                    }
                return $images;
            } catch (PDOException $e) {
                return  $sql . "<br>" . $e->getMessage();
            }
        }

        function LoadProducts($data = null){
                try {
                    $db = new DB();

                    if(empty($data)){
                        $sql = "CALL GetProducts()";
                        $sth = $db->select($sql);
                    }
                    else{
                        if(!empty($data['child_category'])){
                            $sql = "SELECT P.*, C.name AS 'category_name' FROM Products AS P, Categories AS C 
                            WHERE P.category_id = C.category_id AND C.name = ? ";
                            $params = array($data['child_category']);
                        }
                        else if(!empty($data['parent_category'])){
                            $sql = "SELECT P.*, TMP2.name AS 'category_name' FROM Products AS P, 
                            (SELECT C2.* FROM Categories AS C2, 
                            (SELECT C.category_id as parent_id 
                            FROM `Categories` AS C WHERE C.name = ?
                            ) AS TMP 
                            WHERE TMP.parent_id = C2.parent_category_id
                            ) AS TMP2 
                            WHERE P.category_id = TMP2.category_id;";
                            $params = array($data['parent_category']);
                        }
                        else if(!empty($data['product_code'])){
                            $sql = "SELECT P.*, C.name AS 'category_name' FROM Products AS P, Categories AS C 
                            WHERE P.category_id = C.category_id AND P.product_code = ?";
                            $params = array($data['product_code']);
                        }
                        else{
                            $sql = "SELECT P.*, C.name AS 'category_name' FROM Products AS P, Categories AS C 
                            WHERE P.category_id = C.category_id AND 1 = ?";
                            $params = array(1);
                        }

                        $sth = $db->select($sql, $params);
                    }

                    $arr = [];
                    $product_from_DB = $sth->fetchAll();
                   
                    $sth = null;

                    foreach ($product_from_DB as $row) {

                        // tạo sản phẩm
                        $obj = new ProductObj($row);

                        // lấy hình
                        $images = $this->LoadProductImages($obj->getProduct_code());
                        $obj->setImages($images);

                        // lấy size và số lượng
                        $sizes = $this->LoadProductSizes($obj->getProduct_code());
                        $obj->setSizes($sizes);

                        // set số lượng
                        $obj->setQuantity($obj->calculateQuantity());
                        
                        // thêm obj vào mảng
                        $arr[] = $obj;
                    }
                    return $arr;
                } catch (PDOException $e) {
                    return  $sql . "<br>" . $e->getMessage();
                }
        }

        function FindProducts($name){
            try {
                $db = new DB();
                $sql = "SELECT P.*, C.name AS 'category_name' FROM Products AS P, Categories AS C 
                WHERE P.category_id = C.category_id AND P.name LIKE ?;";
                $params = array("%".$name."%");
                $sth = $db->select($sql, $params);

                $arr = [];
                $product_from_DB = $sth->fetchAll();
               
                $sth = null;

                foreach ($product_from_DB as $row) {

                    // tạo sản phẩm
                    $obj = new ProductObj($row);

                    // lấy hình
                    $images = $this->LoadProductImages($obj->getProduct_code());
                    $obj->setImages($images);

                    // lấy size và số lượng
                    $sizes = $this->LoadProductSizes($obj->getProduct_code());
                    $obj->setSizes($sizes);

                    // set số lượng
                    $obj->setQuantity($obj->calculateQuantity());
                    
                    // thêm obj vào mảng
                    $arr[] = $obj;
                }
                return $arr;
            } catch (PDOException $e) {
                return  $sql . "<br>" . $e->getMessage();
            }
        }
        

        function InsertProduct($data){
            try {
                $db = new DB();
                $db->conn->beginTransaction();
                $sql = "INSERT INTO `Products`(`product_code`, `name`, `description`, `price`, `category_id`, `color`) 
                VALUES (?,?,?,?,?,?);";
                $params = array($data['product_code'], $data['product_name'], $data['product_description'], $data['product_price'], $data['category_id'], $data['product_color']);
                $res = $db->execute($sql, $params);
                
                foreach ($data['size_quantities'] as $size => $quantity) {
                        // Thực hiện INSERT vào ProductSizes
                    $res = $this->InsertProductSizes($db, $data['product_code'], $size, $quantity);
                }

                $ordinal_numbers = ['first', 'second', 'third', 'fourth'];
                $index = 0;
               
                foreach ($data['product_images'] as $image) {
                        // Thực hiện INSERT vào ProductSizes

                    $ordinal_number = $ordinal_numbers[$index];
                    $res = $this->InsertProductImages($db, $data['product_code'], $ordinal_number, $image);
                    $index += 1;
                }

                $db->conn->commit();
                return "done";
            } catch (PDOException $e) {
                $db->conn->rollBack();
                if ($e->getCode() == '42000') {
                    // Xử lý khi có lỗi SQLSTATE 42000
                    return "Bạn không có quyền làm thao tác này";
                } else {
                    if($e->getCode() == '22001'){
                        return "Dữ liệu quá dài";
                    }
                    // Xử lý cho các lỗi khác
                    return "Lỗi: " . $e->getMessage();
                    //return "Lỗi khi thêm sản phẩm";
                }
            }
        }
        function InsertProductSizes($db, $product_code, $size, $quantity){
            try{
                $sql = "INSERT INTO `ProductSizes`(`product_code`, `size`, `quantity`) VALUES (?,?,?);";
                $params = array($product_code, $size, $quantity);
                $db->execute($sql, $params);
            }
            catch (PDOException $e) {
                throw $e; // Ném ngoại lệ để bắt ở nơi gọi hàm
                //throw "Lỗi khi thêm size"; // Ném ngoại lệ để bắt ở nơi gọi hàm
            }
        }

        function InsertProductImages($db, $product_code, $ordinal_number, $image){
            try{
                $sql = "INSERT INTO `ProductImages`(`product_code`, `ordinal_number`, `image`) VALUES (?,?,?);";
                $params = array($product_code, $ordinal_number, $image);
                $db->execute($sql, $params);
            }
            catch (PDOException $e) {
                throw $e; // Ném ngoại lệ để bắt ở nơi gọi hàm
                //throw "Lỗi khi thêm ảnh"; // Ném ngoại lệ để bắt ở nơi gọi hàm
            }
        }
        function EditProductSizes($db, $product_code, $size, $quantity){
            try{
                $sql = "UPDATE `ProductSizes` SET `quantity` = ? WHERE `product_code` = ? AND `size` = ? ;";
                $params = array($quantity, $product_code, $size);
                $db->execute($sql, $params);
            }
            catch (PDOException $e) {
                throw $e; // Ném ngoại lệ để bắt ở nơi gọi hàm
                //throw "Lỗi khi sửa size"; // Ném ngoại lệ để bắt ở nơi gọi hàm
            }
        }
    
        function EditProductImages($db, $product_code, $ordinal_number, $image){
            try{
                $sql = "UPDATE `ProductImages` SET `image` = ? WHERE `product_code` = ? AND `ordinal_number` = ?;";
                $params = array($image, $product_code, $ordinal_number);
                $db->execute($sql, $params);
            }
            catch (PDOException $e) {
                throw $e; // Ném ngoại lệ để bắt ở nơi gọi hàm
                //echo  $sql . "<br>" . $e->getMessage();
                //throw "Lỗi khi sửa ảnh";
            }
        }
    
        function EditProduct($data,$editImages){
            try {
                $db = new DB();
                $db->conn->beginTransaction();
    
                $sql = "UPDATE `Products` SET `name` = ?, `description` = ?, `price` = ?, `category_id` = ?, `color` = ? WHERE `product_code` = ?;";
                $params = array($data['product_name'], $data['product_description'], $data['product_price'], $data['category_id'], $data['product_color'], $data['product_code']);
                $db->execute($sql, $params);
    
                foreach ($data['size_quantities'] as $size => $quantity) {
                    // Thực hiện INSERT vào ProductSizes
                    $res = $this->EditProductSizes($db, $data['product_code'], $size, $quantity);
                }
    
                $ordinal_numbers = ['first', 'second', 'third', 'fourth'];
                $index = 0;
                if($editImages == "true"){
                    foreach ($data['product_images'] as $image) {
                        // Thực hiện INSERT vào ProductSizes
                        
                        $ordinal_number = $ordinal_numbers[$index];
                        $res = $this->EditProductImages($db, $data['product_code'], $ordinal_number, $image);
                        $index += 1;
                    }   
        
                }
               
                $db->conn->commit();
    
                return "done";
            } catch (PDOException $e) {
                $db->conn->rollBack();
                if ($e->getCode() == '42000') {
                    // Xử lý khi có lỗi SQLSTATE 42000
                    return "Bạn không có quyền làm thao tác này";
                } else {
                    if($e->getCode() == '22001'){
                        return "Dữ liệu quá dài";
                    }
                    //echo "Lỗi: " . $e->getMessage();
                    return "Lỗi khi sửa sản phẩm";
                }
            }
        }
        function DeleteProduct($data){
            try {
                $db = new DB();
                $db->conn->beginTransaction();
    
                // tìm đơn hàng còn chứa sản phẩm mà đã thanh toán
                //$orders_code =  $this->FindOrder($db, $data['product_code']);
    
                // xóa các đơn hàng này
                //foreach($orders_code as $each){
                  //  $this->DeleteOrder($db, $each);
                //}
    
                // xóa sản phẩm
                $sql = "DELETE FROM `Products` WHERE `product_code` = ?;";
                $params = array($data['product_code']);
                $db->execute($sql, $params);
    
                $db->conn->commit();
                return "done";
            } catch (PDOException $e) {
    
                $db->conn->rollBack();
                if ($e->getCode() == '42000') {
                    // Xử lý khi có lỗi SQLSTATE 42000
                    return "Bạn không có quyền làm thao tác này";
                } else {
                    if($e->getCode() == '22001'){
                        return "Dữ liệu quá dài";
                    }
                    //echo "Lỗi: " . $e->getMessage();
                    return "Lỗi khi xóa sản phẩm";
                }
            }
        }
    }
    
    
?>