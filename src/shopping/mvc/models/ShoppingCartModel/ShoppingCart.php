<?php

class ShoppingCart extends DB
{
    function FindCartCode($data)
    {
        try {
            $db = new DB();
            $sql = "SELECT cart_code FROM ShoppingCart WHERE email = ?";
            $params = array($data['email']);
            $sth = $db->select($sql, $params);
            $arr = [];
            $cartcode = $sth->fetchAll();
            $arr['cart_code'] = $cartcode[0]['cart_code'];
            return $arr;
        } catch (PDOException $e) {
            return null;
        }
    }
}
