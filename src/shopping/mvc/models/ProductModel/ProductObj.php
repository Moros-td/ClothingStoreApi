<?php
include_once "./mvc/models/CategoryModel/CategoryObj.php";
    class ProductObj{
        // private $product_code;
        // private $name;
        // private $description;
        // private $price;
        // private $categoryObj;
        // private $color;
        // private $sizes;
        // private $images;
        // private $quantity;
        // private $update_latest;

        public $product_code;
        public $name;
        public $description;
        public $price;
        public $categoryObj;
        public $color;
        public $sizes;
        public $images;
        public $quantity;
        public $update_latest;

        public function __construct($row)
        {
            $this->product_code = $row['product_code'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            // parent_category_id cho là 0 vì không cần dùng
            // sau này lỗi hoặc cần dùng có thể sửa store proceduce để thêm cột đó là xong
            $data['category_id'] = $row['category_id'];
            $data['name'] = $row['category_name'];
            $data['parent_category_id'] = null;
            $data['parent_category_name'] = null;

            $this->categoryObj = new CategoryObj($data);
            $this->color = $row['color'];
            $this->update_latest = $row['update_latest'];
        }

        public function calculateQuantity(){
                $total = 0;
                foreach($this->sizes as $size => $quantity){
                        $total += $quantity;
                }
                return $total;
        }

        public function getProduct_code()
        {
                return $this->product_code;
        }

        public function setProduct_code($product_code)
        {
                $this->product_code = $product_code;
        }

        public function getName()
        {
                return $this->name;
        }

        public function setName($name)
        {
                $this->name = $name;

        }

        public function getDescription()
        {
                return $this->description;
        }

        public function setDescription($description)
        {
                $this->description = $description;
        }

        public function getPrice()
        {
                return $this->price;
        }

        public function setPrice($price)
        {
                $this->price = $price;

        }

        public function getColor()
        {
                return $this->color;
        }

        public function setColor($color)
        {
                $this->color = $color;
        }

        public function getSizes()
        {
                return $this->sizes;
        }

        public function setSizes($sizes)
        {
                $this->sizes = $sizes;

        }

        public function getImages()
        {
                return $this->images;
        }

        public function setImages($images)
        {
                $this->images = $images;

        }

        public function getQuantity()
        {
                return $this->quantity;
        }

        public function setQuantity($quantity)
        {
                $this->quantity = $quantity;
        }

        public function getUpdate_lastest()
        {
                return $this->update_latest;
        }

        public function setUpdate_latest($update_latest)
        {
                $this->update_latest = $update_latest;

        }

        public function getCategoryObj()
        {
                return $this->categoryObj;
        }

        public function setCategoryObj($categoryObj)
        {
                $this->categoryObj = $categoryObj;
        }
    }
?>