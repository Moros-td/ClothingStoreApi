<?php
include_once "./mvc/models/OrderModel/OrderObj.php";

class Order extends DB
{
    function getAllOrders()
    {
        try {
            $db = new DB();
            $sql = "SELECT TMP.*, P.payment_code, P.payment_date, P.type 
            FROM (
                SELECT O.*, C.phone, C.full_name
                FROM Orders AS O, Customers AS C 
                WHERE O.email = C.email
                ) AS TMP 
                LEFT JOIN Payment AS P 
                ON P.order_code = TMP.order_code";
            $sth = $db->select($sql);
            $orders = [];
            $order_from_DB = $sth->fetchAll();
            foreach ($order_from_DB as $row) {
                // thêm obj vào mảng

                if ($row['type'] == 'bank_transfer') {
                    $row['type'] = 'Chuyển khoản';
                } else if ($row['type'] == 'cash') {
                    $row['type'] = 'Tiền mặt';
                } else {
                    $row['type'] = 'Chưa thanh toán';
                }
                $obj = new OrderObj($row);
                $orders_item = $this->getAllOrdersItem($row['order_code']);

                $dataCustomer = [];
                $dataCustomer['email'] = $row['email'];
                $dataCustomer['phone'] = $row['phone'];
                $dataCustomer['full_name'] = $row['full_name'];
                $customer = new CustomerObj($dataCustomer);

                $obj->setOrder_items($orders_item);
                $obj->setCustomer($customer);
                $orders[] = $obj;
            }

            return $orders;
        } catch (PDOException $e) {
            return "Lỗi khi load đơn hàng";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    function getAllOrdersItem($order_code)
    {
        try {
            $db = new DB();
            $sql = "SELECT OI.* FROM OrderItems AS OI, Orders AS O 
            WHERE OI.order_code = O.order_code AND OI.order_code = ?;";
            $params = array($order_code);
            $sth = $db->select($sql, $params);
            $orders_items = [];
            $orders_item_from_DB = $sth->fetchAll();
            foreach ($orders_item_from_DB as $row) {
                // thêm obj vào mảng

                $obj = new OrderItemObj($row);
                $product = $this->LoadProductInfo($row['product_code']);
                $obj->setProduct($product);
                $orders_items[] = $obj;
            }

            return $orders_items;
        } catch (PDOException $e) {
            return "Lỗi khi load chi tiết đơn hàng";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    function LoadProductInfo($product_code)
    {
        try {
            $db = new DB();
            $sql = "SELECT * FROM Products AS P WHERE P.product_code = ?;";
            $params = array($product_code);
            $sth = $db->select($sql, $params);
            $products_items = [];
            $products_item_from_DB = $sth->fetchAll();
            foreach ($products_item_from_DB as $row) {
                // thêm obj vào mảng

                $obj = new ProductObj($row);
                $images = $this->LoadProductImages($obj->getProduct_code());
                $obj->setImages($images);
                $products_items = $obj;
            }

            return $products_items;
        } catch (PDOException $e) {
            return "Lỗi khi load hình";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    function LoadProductImages($product_code)
    {
        try {
            $db = new DB();
            $sql = "SELECT * FROM ProductImages AS PI WHERE PI.product_code = ?;";
            $params = array($product_code);
            $sth = $db->select($sql, $params);
            $imgs_items = [];
            $imgs_item_from_DB = $sth->fetchAll();
            foreach ($imgs_item_from_DB as $row) {
                // thêm obj vào mảng

                $imgs_items[] = $row['image'];
            }

            return $imgs_items;
        } catch (PDOException $e) {
            return "Lỗi khi load hình";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    // load lịch sử đơn hàng
    function LoadOrderHistory()
    {
        try {
            $db = new DB();
            $sql = "SELECT OH.*, C.full_name FROM OrdersHistory AS OH, Customers as C WHERE OH.email = C.email";
            $params = array();
            $sth = $db->select($sql, $params);
            $orders = [];
            $order_from_DB = $sth->fetchAll();
            foreach ($order_from_DB as $row) {
                // thêm obj vào mảng
                $row['type'] = $row['payment_type'];

                if ($row['type'] == 'bank_transfer') {
                    $row['type'] = 'Chuyển khoản';
                } else if ($row['type'] == 'cash') {
                    $row['type'] = 'Tiền mặt';
                } else {
                    $row['type'] = 'Chưa thanh toán';
                }
                $obj = new OrderObj($row);
                $orders_item = $this->LoadOrderHistoryItem($row['order_code']);

                $dataCustomer = [];
                $dataCustomer['email'] = $row['email'];
                $dataCustomer['phone'] = $row['phone'];
                $dataCustomer['full_name'] = $row['full_name'];
                $customer = new CustomerObj($dataCustomer);

                $obj->setOrder_items($orders_item);
                $obj->setCustomer($customer);
                $orders[] = $obj;
            }

            return $orders;
        } catch (PDOException $e) {
            return "Lỗi khi load lịch sử đơn hàng";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    // load chi tiết lịch sử đơn hàng
    function LoadOrderHistoryItem($order_code)
    {
        try {
            $db = new DB();
            $sql = "SELECT OHI.* FROM OrdersHistoryItems AS OHI, OrdersHistory AS OH 
                WHERE OHI.order_code = OH.order_code AND OHI.order_code = ?;";
            $params = array($order_code);
            $sth = $db->select($sql, $params);
            $orders_items = [];
            $orders_item_from_DB = $sth->fetchAll();
            foreach ($orders_item_from_DB as $row) {
                // thêm obj vào mảng

                $obj = new OrderItemObj($row);
                $product = $this->LoadProductInfo($row['product_code']);
                $obj->setProduct($product);
                $orders_items[] = $obj;
            }

            return $orders_items;
        } catch (PDOException $e) {
            return "Lỗi khi load chi tiết lịch sử đơn hàng";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    // di chuyển đơn hàng vào history
    function MoveOrderToHistory($db, $order)
    {
        try {
            if (empty($order->getPayment_code())) {
                $sql = "INSERT INTO `OrdersHistory`(`order_code`, `order_date`, `state`, `email`, `address`, `phone`, `total_price`) 
                    VALUES (?,?,?,?,?,?,?);";
                $params = array($order->getOrder_code(), $order->getOrder_date(), $order->getState(), $order->getCustomer()->getEmail(), $order->getAddress(), $order->getCustomer()->getPhone(), $order->getTotal_price());
            } else {
                $sql = "INSERT INTO `OrdersHistory`(`order_code`, `order_date`, `payment_code`, `payment_date`, `payment_type`, `state`, `email`, `address`, `phone`, `total_price`)
                    VALUES (?,?,?,?,?,?,?,?,?,?);";
                $params = array($order->getOrder_code(), $order->getOrder_date(), $order->getPayment_code(), $order->getPayment_date(), $order->getType(), $order->getState(), $order->getCustomer()->getEmail(), $order->getAddress(), $order->getCustomer()->getPhone(), $order->getTotal_price());
            }
            $db->execute($sql, $params);
        } catch (PDOException $e) {
            //echo "Lỗi khi đổi trạng thái thành vận chuyển";
            throw $e;
            // echo "Lỗi khi đổi trạng thái thành vận chuyển";
            //echo  $sql . "<br>" . $e->getMessage();
        }
    }

    // di chuyển chi tiết đơn hàng vào history
    function MoveOrderToHistoryDetails($db, $data)
    {
        try {
            // var_dump($data);
            $sql = "INSERT INTO `OrdersHistoryItems`(`order_code`, `product_code`, `quantity`, `size`, `total_price`) 
                VALUES (?,?,?,?,?);";
            $params = array($data->getOrder_code(), $data->getProduct()->getProduct_code(), $data->getQuantity(), $data->getSize(), $data->getTotal_price());

            $db->execute($sql, $params);
        } catch (PDOException $e) {
            throw $e;
            //echo  $sql . "<br>" . $e->getMessage();
        }
    }

    function ChangeQuantityProduct($db, $data)
    {
        try {
            // var_dump($data);
            $sql = "UPDATE `ProductSizes` AS PS SET PS.quantity = PS.quantity + ? WHERE PS.size = ? AND PS.product_code = ? ";
            $params = array($data->getQuantity(), $data->getSize(), $data->getProduct()->getProduct_code());

            $db->execute($sql, $params);
        } catch (PDOException $e) {
            throw $e;
            //echo  $sql . "<br>" . $e->getMessage();
        }
    }

    // tìm order theo order code
    function FindOrder($order_code, $db = null)
    {
        try {
            if ($db == null)
                $db = new DB();
            $sql = "SELECT TMP.*, P.payment_code, P.payment_date, P.type 
                FROM (
                    SELECT O.*, C.phone, C.full_name
                    FROM Orders AS O, Customers AS C 
                    WHERE O.email = C.email AND O.order_code = ?
                    ) AS TMP 
                    LEFT JOIN Payment AS P 
                    ON P.order_code = TMP.order_code";
            $params = array($order_code);
            $sth = $db->select($sql, $params);
            $orders = [];
            $order_from_DB = $sth->fetchAll();

            foreach ($order_from_DB as $row) {
                // thêm obj vào mảng

                $obj = new OrderObj($row);

                $orders_item = $this->FindOrdersItem($db, $row['order_code']);

                $dataCustomer = [];
                $dataCustomer['email'] = $row['email'];
                $dataCustomer['phone'] = $row['phone'];
                $customer = new CustomerObj($dataCustomer);

                $obj->setOrder_items($orders_item);
                $obj->setCustomer($customer);
                $orders[] = $obj;
            }

            return $orders;
        } catch (PDOException $e) {
            return "Lỗi khi load đơn hàng";
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    function FindOrdersItem($db, $order_code)
    {
        try {
            $sql = "SELECT OI.* FROM OrderItems AS OI, Orders AS O 
                WHERE OI.order_code = O.order_code AND OI.order_code = ?;";
            $params = array($order_code);
            $sth = $db->select($sql, $params);
            $orders_items = [];
            $orders_item_from_DB = $sth->fetchAll();
            foreach ($orders_item_from_DB as $row) {
                // thêm obj vào mảng

                $obj = new OrderItemObj($row);
                $product = $this->LoadProductInfo($row['product_code']);
                $obj->setProduct($product);
                $orders_items[] = $obj;
            }

            return $orders_items;
        } catch (PDOException $e) {
            throw $e;
            //return  $sql . "<br>" . $e->getMessage();
        }
    }

    // hủy đơn hàng
    function CancelOrder($data)
    {
        try {
            $db = new DB();
            $db->conn->beginTransaction();

            // update trạng thái thành cancel
            $sql = "UPDATE `Orders` AS O SET O.state='cancelled' WHERE O.order_code = ?;";
            $params = array($data['orderCode']);
            $db->execute($sql, $params);

            // load order
            $order = $this->FindOrder($data['orderCode'], $db);

            // di chuyển order qua history
            $this->MoveOrderToHistory($db, $order[0]);

            // // load order details
            $order_details = $order[0]->getOrder_items();

            // di chuyển từng cái qua history
            foreach ($order_details as $each) {
                $this->MoveOrderToHistoryDetails($db, $each);

                // trả số lượng về cho bảng sản phẩm
                $this->ChangeQuantityProduct($db, $each);
            }

            // xóa order
            $sql = "DELETE FROM `Orders` AS O WHERE O.order_code = ?;";
            $params = array($data['orderCode']);
            $db->execute($sql, $params);

            $db->conn->commit();
            return "done";
        } catch (PDOException $e) {
            $db->conn->rollBack();
            return $e;
            //echo $e->getMessage();
        }
    }
    // Thay đổi trạng thái của đơn hàng từ "pending" sang "delivering"
    function Delivering($data)
    {
        try {
            $db = new DB();
            $sql = "UPDATE `Orders` AS O SET O.state='delivering' WHERE O.order_code = ?;";
            $params = array($data['orderCode']);
            $db->execute($sql, $params);
            return "done";
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    // Thay đổi trạng thái của đơn hàng từ "delivering" sang "delivered"
    function Delivered($data)
    {
        try {
            $db = new DB();
            $db->conn->beginTransaction();

            // update trạng thái thành cancel
            $sql = "UPDATE `Orders` AS O SET O.state='delivered' WHERE O.order_code = ?;";
            $params = array($data['orderCode']);
            $db->execute($sql, $params);

            // load order
            $order = $this->FindOrder($data['orderCode'], $db);

            // di chuyển order qua history
            $this->MoveOrderToHistory($db, $order[0]);

            // // load order details
            $order_details = $order[0]->getOrder_items();

            // di chuyển từng cái qua history
            foreach ($order_details as $each) {
                $this->MoveOrderToHistoryDetails($db, $each);

                // trả số lượng về cho bảng sản phẩm
                $this->ChangeQuantityProduct($db, $each);
            }

            // xóa order
            $sql = "DELETE FROM `Orders` AS O WHERE O.order_code = ?;";
            $params = array($data['orderCode']);
            $db->execute($sql, $params);

            $db->conn->commit();
            return "done";
        } catch (PDOException $e) {
            $db->conn->rollBack();
            return $e;
            //echo $e->getMessage();
        }
    }
}
