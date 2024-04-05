<?php
include_once "./mvc/models/OrderModel/OrderObj.php";

class Order extends MiddleWare {
    function LoadOrders(){
        try {
            $db = new DB();
            $sql = "SELECT O.order_code,`order_date`, `state`, `total_price`,  JSON_ARRAYAGG(JSON_OBJECT( 'productName',L.name,'quantity', `quantity`)) AS list
            FROM `Orders` AS O
            JOIN (SELECT `quantity`, `name`, OI.order_code
                FROM `OrderItems` AS OI JOIN `Products` p ON oi.product_code = p.product_code) AS L
            ON o.order_code = L.order_code
            GROUP BY o.order_code";
            $sth = $db->select($sql);
            $arr = [];
            $orders_from_DB = $sth->fetchAll();
            foreach ($orders_from_DB as $row) {
                $obj = new OrderObj($row);
                $arr[] = $obj;
            }
            return $arr;
        } catch (PDOException $e) {
            return  $sql . "<br>" . $e->getMessage();
        }
    } 
}
?>
