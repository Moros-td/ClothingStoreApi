<?php
include_once "./mvc/models/CartItemModel/CartItemObj.php";
class CartItem extends DB
{

    function LoadProductImages($product_code)
    {
        try {
            $db = new DB();
            $sql = "CALL GetImagesProduct(?)";
            $params = array($product_code);
            $sth = $db->select($sql, $params);
            $images = [];
            while ($row2 = $sth->fetch()) {
                $images[] = $row2['image'];
            }
            return $images;
        } catch (PDOException $e) {
            return  "Lỗi";
        }
    }

    function LoadCartItem($data)
    {
        try {
            $db = new DB();
            $sql = "SELECT TMP.*, P.name, P.price, P.color FROM 
                    (
                        SELECT CI.* FROM ShoppingCart AS SC, CartItems AS CI 
                        WHERE SC.cart_code = CI.cart_code AND SC.cart_code = ?
                        ) AS TMP, Products AS P 
                        WHERE P.product_code = TMP.product_code";
            $params = array($data['cart_code']);

            $sth = $db->select($sql, $params);

            $arr = [];
            $cartItem_from_DB = $sth->fetchAll();

            $sth = null;

            foreach ($cartItem_from_DB as $row) {

                $obj_cartItem = new CartItemObj($row);

                $row_product['product_code'] = $row['product_code'];
                $row_product['name'] = $row['name'];
                $row_product['price'] = $row['price'];
                $row_product['color'] = $row['color'];
                $quantityData = [
                    'product_code' => $row['product_code'],
                    'size' => $row['size']
                ];
                $quantity = $this->FindQuantity($quantityData);
                $row_product['quantity'] = isset($quantity[0]) ? $quantity[0] : null;
                // tạo sản phẩm
                $obj = new ProductObj($row_product);

                // lấy hình

                $images = $this->LoadProductImages($row['product_code']);
                $obj->setImages($images);

                $obj_cartItem->setProduct($obj);

                // thêm obj vào mảng
                $arr[] = $obj_cartItem;
            }
            return $arr;
        } catch (PDOException $e) {
            return  "Lỗi";
        }
    }

    function FindPrice($product_code)
    {
        try {
            $db = new DB();
            $sql = "SELECT P.price FROM Products AS P WHERE P.product_code = ?";
            $params = array($product_code);
            $sth = $db->select($sql, $params);
            while ($row = $sth->fetch()) {
                $price[] = $row['price'];
            }
            return $price;
        } catch (PDOException $e) {
            $a = [];
            return $a;
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    function FindQuantity($data)
    {
        try {
            $db = new DB();
            $sql = "SELECT quantity FROM ProductSizes WHERE product_code = ? AND size = ?";
            $params = array($data['product_code'], $data['size']);
            $sth = $db->select($sql, $params);
            $quantity = $sth->fetchAll();
            return $quantity[0]['quantity'];
        } catch (PDOException $e) {
            $a = [];
            return $a;
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    function AddProduct($data)
    {
        try {
            $db = new DB();
            $check_sql = "SELECT COUNT(quantity) AS num, SUM(quantity) AS quantity, SUM(total_price) AS total_price FROM CartItems WHERE product_code = ? AND size = ? AND cart_code =?";
            $check_params = array($data['product_code'], $data['size'], $data['cart_code']);
            $sth = $db->select($check_sql, $check_params);
            $result = $sth->fetchAll();
            $num = $result[0]['num'];
            $quantity = $result[0]['quantity'];
            $total_price = $result[0]['total_price'];

            if ($num > 0) {
                $data['quantity'] += $quantity; // Cộng thêm số lượng mới vào số lượng hiện có
                $data['total_price'] += $total_price;
                $this->ChangeQuantityAndPrice($data);
                return "done";
            } else {
                $sql = "INSERT INTO `CartItems`(`cart_code`, `product_code`, `quantity`, `size`, `total_price`) VALUES (?,?,?,?,?)";
                $params = array($data['cart_code'], $data['product_code'], $data['quantity'], $data['size'], $data['total_price']);
                $db->execute($sql, $params);
                return "done";
            }
        } catch (PDOException $e) {
            return "lỗi";
        }
    }
    function RemoveProduct($data)
    {
        try {
            $db = new DB();
            $check_sql = "SELECT COUNT(quantity) AS num, SUM(quantity) AS quantity, SUM(total_price) AS total_price FROM CartItems WHERE product_code = ? AND size = ? AND cart_code =?";
            $check_params = array($data['product_code'], $data['size'], $data['cart_code']);
            $sth = $db->select($check_sql, $check_params);
            $result = $sth->fetchAll();
            $num = $result[0]['num'];
            $quantity = $result[0]['quantity'];
            $total_price = $result[0]['total_price'];

            //Giảm số lượng            
            $data['quantity'] = $quantity - $data['quantity'];
            $data['total_price'] = $total_price - $data['total_price'];
            $this->ChangeQuantityAndPrice($data);
            return "done";
        } catch (PDOException $e) {
            return "lỗi";
        }
    }


    function ChangeQuantityAndPrice($data)
    {
        try {
            $db = new DB();
            $sql = "UPDATE CartItems AS CI SET CI.quantity= ? ,CI.total_price = ? WHERE CI.cart_code = ? AND CI.product_code = ? AND CI.size = ?";
            $params = array($data['quantity'], $data['total_price'], $data['cart_code'], $data['product_code'], $data['size']);
            $db->execute($sql, $params);
            return "done";
        } catch (PDOException $e) {
            return "Lỗi khi update số lượng";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    function DeleteProductInCart($data)
    {
        try {
            $db = new DB();
            $sql = "DELETE FROM CartItems AS CI WHERE CI.cart_code = ? AND CI.product_code = ? AND CI.size = ?";
            $params = array($data['cart_code'], $data['product_code'], $data['size']);
            $db->execute($sql, $params);
            return "done";
        } catch (PDOException $e) {
            return "Lỗi khi xóa sản phẩm";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }
    function CheckTotalProduct($cart_code){
        try{
            $db = new DB();
            $sql = "SELECT SUM(quantity) AS total_quantity FROM CartItems WHERE cart_code =?";     
            $params = array($cart_code);
            $sth = $db->select($sql, $params);
            $result = $sth->fetchAll();
            $total_product = $result[0]['total_quantity'];
            
            return $total_product;
           
        }
        catch(PDOException $e){
            return null;
        }

    }
    function DeleteAll($data)
    {
        try {
            $db = new DB();
            $sql = "DELETE FROM CartItems AS CI WHERE CI.cart_code = ?";
            $params = array($data['cart_code']);
            $db->execute($sql, $params);
            return "done";
        } catch (PDOException $e) {
            return "Lỗi khi xóa sản phẩm";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }
}
