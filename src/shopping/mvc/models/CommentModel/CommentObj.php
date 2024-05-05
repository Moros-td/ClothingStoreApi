<?php
    class CommentObj{
        public $comment_id;
        public $email;
        public $product_code;
        public $rating;
        public $comment;
        public $created_at;

        public function __construct($row)
        {
            $this->comment_id = $row['comment_id'];
            $this->email = $row['email'];
            $this->product_code = $row['product_code'];
            $this->rating = $row['rating'];
            $this->comment = $row['comment'];
            $this->created_at = $row['created_at'];
        }

        
        public function getComment_id()
        {
                return $this->comment_id;
        }

        public function setComment_id($comment_id)
        {
                $this->comment_id = $comment_id;

                return $this;
        }

        public function getEmail()
        {
                return $this->email;
        }

        public function setEmail($email)
        {
                $this->email = $email;

                return $this;
        }

        public function getProduct_code()
        {
                return $this->product_code;
        }

        public function setProduct_code($product_code)
        {
                $this->product_code = $product_code;

                return $this;
        }

        public function getRating()
        {
                return $this->rating;
        }

        public function setRating($rating)
        {
                $this->rating = $rating;

                return $this;
        }

        public function getComment()
        {
                return $this->comment;
        }

        public function setComment($comment)
        {
                $this->comment = $comment;

                return $this;
        }

        public function getCreated_at()
        {
                return $this->created_at;
        }

        public function setCreated_at($created_at)
        {
                $this->created_at = $created_at;

                return $this;
        }
    }

?>