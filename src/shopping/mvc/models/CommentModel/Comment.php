<?php
include_once "./mvc/models/CommentModel/CommentObj.php";
class Comment extends DB
{
    function LoadAllCommentForProduct($product_code)
    {
        try {
            $db = new DB();
            $sql = "SELECT * FROM `Comments` AS C WHERE C.product_code = ? ORDER BY C.created_at DESC;";
            $params = array($product_code);
            $sth = $db->select($sql, $params);
            $arr = $sth->fetchAll();

            return $arr;
        } catch (PDOException $e) {
            return  $sql . "<br>" . $e->getMessage();
        }
    }

    function UpdateCommentState($db, $orderItemId){
        try{
            $sql = "UPDATE OrdersHistoryItems SET commentState = TRUE WHERE history_detail_id = ?;";
            $params = array($orderItemId);
            $db->execute($sql, $params);
        }
        catch (PDOException $e) {
            throw $e; // Ném ngoại lệ để bắt ở nơi gọi hàm
            //throw "Lỗi khi thêm size"; // Ném ngoại lệ để bắt ở nơi gọi hàm
        }
    }

    function AddComment($data)
    {
        try {
            $db = new DB();
            $db->conn->beginTransaction();
            $sql = "INSERT INTO `Comments`(`email`, `rating`, `comment`, `product_code`)
             VALUES (?,?,?,?)";
            $params = array($data['email'], $data['rating'], $data['comment'], $data['productCode']);
            $sth = $db->execute($sql, $params);

            $this->UpdateCommentState($db, $data['orderItemId']);
            $db->conn->commit();
            return "done";
        } catch (PDOException $e) {
            $db->conn->rollBack();
            return  $sql . "<br>" . $e->getMessage();
        }
    }
}
