<?php
class Statistical extends MiddleWare {
    function getOrderDetails() {
        try {
            $db = new DB();
            $sql = "SELECT order_code, DATE(order_date), total_price
                    FROM OrdersHistory
                    WHERE state = 'delivered'";
            $sth = $db->select($sql);
            $data = $sth->fetchAll();
            return $data;
        } catch (PDOException $e) {
            return "Lỗi khi truy vấn cơ sở dữ liệu: " . $e->getMessage();
        }
    }
    function getRevenueByMonth() {
        try {
            $db = new DB();
            $sql = "SELECT YEAR(order_date) AS year, MONTH(order_date) AS month, SUM(total_price) AS revenue 
                    FROM OrdersHistory 
                    WHERE state = 'delivered' AND order_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) 
                    GROUP BY YEAR(order_date), MONTH(order_date)
                    ORDER BY year ASC, month ASC"; 
            $sth = $db->select($sql);
            $data = $sth->fetchAll();
            return $data;
        } catch (PDOException $e) {
            return "Lỗi khi truy vấn cơ sở dữ liệu: " . $e->getMessage();
        }
    }

    function getRevenueByYear() {
        try {
            $db = new DB();
            $sql = "SELECT YEAR(order_date) AS year, SUM(total_price) AS revenue 
                    FROM OrdersHistory 
                    WHERE state = 'delivered' 
                    GROUP BY YEAR(order_date)
                    ORDER BY YEAR(order_date) ASC 
                    LIMIT 5"; 
            $sth = $db->select($sql);
            $data = $sth->fetchAll();
            return $data;
        } catch (PDOException $e) {
            return "Lỗi khi truy vấn cơ sở dữ liệu: " . $e->getMessage();
        }
    }

    function getTopSellingProducts() {
        try {
            $db = new DB();
            $sql = "SELECT P.name AS product_name, SUM(OI.quantity) AS total_quantity 
                    FROM OrdersHistory AS OH
                    INNER JOIN OrdersHistoryItems AS OI ON OH.order_code = OI.order_code
                    INNER JOIN Products AS P ON OI.product_code = P.product_code
                    WHERE OH.state = 'delivered'
                    GROUP BY OI.product_code 
                    ORDER BY total_quantity DESC 
                    LIMIT 5";
            $sth = $db->select($sql);
            $data = $sth->fetchAll();
            return $data;
        } catch (PDOException $e) {
            return "Lỗi khi truy vấn cơ sở dữ liệu: " . $e->getMessage();
        }
    }
    
}
?>
