<?php
    class CategoryObj{
        public $category_id;
        public $name;
        public $parent_category;

        public function __construct($row)
        {
            $this->category_id = $row['category_id'];
            $this->name = $row['name'];
        }

        public function getCategory_id()
        {
                return $this->category_id;
        }

        public function setCategory_id($category_id)
        {
                $this->category_id = $category_id;

        }

        public function getName()
        {
                return $this->name;
        }

        public function setName($name)
        {
                $this->name = $name;
        }

        public function getParent_category()
        {
                return $this->parent_category;
        }

        public function setParent_category($parent_category)
        {
                $this->parent_category = $parent_category;

                return $this;
        }
    }
?>