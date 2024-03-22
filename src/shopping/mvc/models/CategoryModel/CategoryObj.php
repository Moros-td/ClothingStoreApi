<?php
    class CategoryObj{
        private $category_id;
        private $name;
        private $parent_category_id;
        private $parent_category_name;

        public function __construct($row)
        {
            $this->category_id = $row['category_id'];
            $this->name = $row['name'];
            $this->parent_category_id = $row['parent_category_id'];
            $this->parent_category_name = $row['parent_name'];
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

        public function getParent_category_id()
        {
                return $this->parent_category_id;
        }

        public function setParent_category_id($parent_category_id)
        {
                $this->parent_category_id = $parent_category_id;

        }

        public function getParent_category_name()
        {
                return $this->parent_category_name;
        }

        public function setParent_category_name($parent_category_name)
        {
                $this->parent_category_name = $parent_category_name;

        }
    }
?>