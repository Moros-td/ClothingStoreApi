<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CartController extends Controller
{

    function AddProduct()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $product_data = [
                'cart_code' => $_POST['cart_code'],
                'product_code' => $_POST['product_code'],
                'quantity' => $_POST['quantity'],
                'size' => $_POST['size'],
                'total_price' => $_POST['total_price'],
            ];
            $product_data = array_map('trim', $product_data);

            $model = $this->model("CartItem");
            $err = $model->AddProduct($product_data);
            if ($err != "done") {
                echo json_encode(['error' => $err], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo json_encode(['success' => 'done'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }
    function LoadCartItem()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = [
            'email' => $_POST['email']
        ];

        // Gọi model để tìm cart_code dựa trên email
        $shoppingCartModel = $this->model("ShoppingCart");
        $cartCodeResult = $shoppingCartModel->FindCartCode($data);
        if (!isset($cartCodeResult['cart_code'])) {
            echo json_encode([], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return;
        }
        $cart_code = $cartCodeResult['cart_code'];
        $cartItemModel = $this->model("CartItem");
        $cartItems = $cartItemModel->LoadCartItem(['cart_code' => $cart_code]);

        // Thêm quantity vào từ hàm FindQuantity
        foreach ($cartItems as $cartItem) {
            $quantityData = [
                'product_code' => $cartItem->getProduct()->getProduct_code(),
                'size' => $cartItem->getSize()
            ];
            $quantity = $cartItemModel->FindQuantity($quantityData);
            $cartItem->getProduct()->setQuantity($quantity); // Thiết lập số lượng sản phẩm        
        }

        echo json_encode($cartItems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}


    function CheckItem()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'email' => $_POST['email']
            ];

            $model = $this->model("ShoppingCart");
            $cartCodeResult = $model->FindCartCode($data);

            if (!isset($cartCodeResult['cart_code'])) {
                $num['numberOfItem'] = 0;
            } else {
                $cartCode = $cartCodeResult['cart_code'];
                $cartItemModel = $this->model("CartItem");
                $arr = $cartItemModel->LoadCartItem(['cart_code' => $cartCode]);
                $num['numberOfItem'] = count($arr);
            }

            $json_response = json_encode($num, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo $json_response;
        }
    }

    function FindCartCode()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'email' => $_POST['email']
            ];
            $model = $this->model("ShoppingCart");
            $arr = $model->FindCartCode($data);
            $json_response = json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo $json_response;
        }
    }
}
