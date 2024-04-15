<?php
class AddressObj
{
    public $address_id;
    public $address;
    public $email;
    public $is_default;
    public $full_name;
    public $phone;

    public function __construct($row)
    {
        $this->address = $row['address'];
        $this->email = $row['email'];
        $this->is_default = $row['is_default'];
    }

    public function getAddress()
    {
            return $this->address;
    }

    public function setAddress($address)
    {
            $this->address = $address;
    }
    public function getEmail()
    {
            return $this->email;
    }

    public function setEmail($email)
    {
            $this->email = $email;
    }
    public function getIs_default()
    {
            return $this->is_default;
    }

    public function setIs_default($is_default)
    {
            $this->is_default = $is_default;
    }
    public function getFull_name()
    {
            return $this->full_name;
    }

    public function setFull_name($full_name)
    {
            $this->full_name = $full_name;
    }
    public function getPhone()
    {
            return $this->phone;
    }

    public function setPhone($phone)
    {
            $this->phone = $phone;
    }

}
?>