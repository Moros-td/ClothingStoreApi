<?php
include_once "./mvc/models/AddressModel/AddressObj.php";
class Address extends DB
{
    function LoadAllAddress($email)
    {
        try {
            $db = new DB();
            $sql = "SELECT A.address_id, A.address, A.email, A.is_default, C.full_name, C.phone FROM Addresses AS A JOIN Customers AS C
                WHERE A.email = C.email AND A.email = ?";
            $params = array($email);
            $sth = $db->select($sql, $params);
            $arr = $sth->fetchAll();

            return $arr;
        } catch (PDOException $e) {
            return  $sql . "<br>" . $e->getMessage();
        }
    }
    function SetAddressDefault($data)
    {
        try {
            $db = new DB();
            $sql = "UPDATE Addresses SET is_default = TRUE WHERE email = ?;                
                UPDATE Addresses SET is_default = FALSE WHERE email = ? AND address_id != ?";
            $params = array($data['email'],$data['email'], $data['address_id']);
            $sth = $db->execute($sql, $params);
            return "done";
        } catch (PDOException $e) {
            return  $sql . "<br>" . $e->getMessage();
        }
    }
}
