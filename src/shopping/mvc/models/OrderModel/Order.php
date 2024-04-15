<?php
include_once "./mvc/models/OrderModel/OrderObj.php";
class Order extends DB
{
    function AddOrder($data)
    {
        try {
            $db = new DB();
            $order_code = self::generateOrderCode($db);
            $order_date = date('Y-m-d H:i:s');

            $sql = "INSERT INTO `Orders`(`order_code`, `order_date`, `state`, `total_price`, `email`, `address`) VALUES (?,?,?,?,?,?)";
            $params = array($order_code, $order_date, $data['state'], $data['total_price'], $data['email'], $data['address']);
            $db->execute($sql, $params);

            return $order_code;
        } catch (PDOException $e) {
            return "lỗi";
        }
    }
    public static function generateOrderCode($db)
    {
        $sql = "SELECT MAX(SUBSTRING(order_code, 3)) as max_order_code FROM Orders";
        $result = $db->select($sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $max_order_code = $row['max_order_code'];
        if ($max_order_code === null) {
            $new_order_code = 1;
        } else {
            $new_order_code = intval($max_order_code) + 1;
        }
        return 'OD' . str_pad($new_order_code, 5, '0', STR_PAD_LEFT);
    }
    function AddOrderItem($data)
    {
        try {
            $db = new DB();
            $sql = "SELECT MAX(order_item_id) AS max_id FROM OrderItems"; // Đặt tên cho cột trả về
            $result = $db->select($sql);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $order_item_id = $row['max_id']; // Lấy giá trị max_id từ kết quả trả về
            if (is_null($order_item_id)) {
                $order_item_id = 1;
            } else {
                $order_item_id++;
            }
            $sql = "INSERT INTO `OrderItems`(`order_item_id`, `order_code`, `product_code`, `quantity`, `size`, `total_price`) VALUES (?,?,?,?,?,?)";
            $params = array($order_item_id, $data['order_code'], $data['product_code'], $data['quantity'], $data['size'], $data['total_price']);
            $db->execute($sql, $params);
            return "done";
        } catch (PDOException $e) {
            // return "lỗi";
            return  $sql . "<br>" . $e->getMessage();
        }
    }
}
