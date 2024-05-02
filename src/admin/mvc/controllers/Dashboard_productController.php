<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Dashboard_productController extends Controller
{
    function getAllProducts()
    {
        $model = $this->model("Product");
        $data = $model->LoadProducts();
        $json_response = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_response;
    }
    function addProduct()
    {
        $response = [
            'success' => false,
            'message' => ''
        ];
        $product_code = "SP" . time();
        sleep(1);
        $size_quantities = array(
            "S" => $_POST['S'],
            "M" => $_POST['M'],
            "L" => $_POST['L'],
            "XL" => $_POST['XL'],
            "XXL" => $_POST['XXL']
        );

        $size_quantities = array_map('trim', $size_quantities);

        $product_data = array(
            "product_code" => $product_code,
            "product_name" => $_POST['product_name'],
            "product_price" => $_POST['product_price'],
            "category_id" => $_POST['category_id'],
            "product_color" => $_POST['color'],
            "product_description" => $_POST['product_description']
        );

        $product_data = array_map('trim', $product_data);

        $product_data["size_quantities"] = $size_quantities;

        $tmp = htmlspecialchars($product_data['product_description']);
        $product_data['product_description'] = $tmp;

        //$check = $this->ValidateProductData($product_data);
        $check = "validated";
        if ($check === "validated") {

            $uploadedFile = $_FILES["file"];

            $fileNames = $this->UpLoadFiles($uploadedFile);
            if (!is_array($fileNames)) {
                echo $fileNames;
            } else {
                // thêm ảnh vào data
                $product_data["product_images"] = $fileNames;

                $model = $this->model("Product");
                $err = $model->InsertProduct($product_data);

                if ($err != "done") {
                    foreach ($fileNames as $each) {
                        unlink($each);
                    }
                } else {
                    $response['success'] = true;
                }
                $response['message'] = $err;
            }

        } else {
            $response['success'] = $check;
        }
        echo json_encode($response);
    }
    function UpLoadFiles($uploadedFile)
    {

        $fileDirs = [];
        $files = [];

        $count = 0;

        if (!isset($uploadedFile)) {
            return "Vui lòng chọn file";
        }

        foreach ($uploadedFile as $key => $values) {
            //print_r($values);
            //echo $key;
            $count += 1;
            if (!is_array($values)) {
                return "File không hợp lệ";
            }
            // if(count($values) != 4){
            //     return "Vui lòng chọn 4 file";
            // }
            foreach ($values as $index => $value) {
                $files[$index][$key] = $value;
            }
        }

        // gộp các thuộc tính của file thành một cụm

        $uploadPath = './public/products/';
        $year = date('Y', time());
        $month = date('m', time());
        $day = date('d', time());
        $uploadPath = $uploadPath . $year . "/" . $month . "/" . $day;

        // nếu chưa có dir thì tạo
        if (!is_dir($uploadPath)) {
            // set quyền và cho phép tạo luôn cả thư mục con và thư mục cha
            mkdir($uploadPath, 0764, true);
        }

        // check valid file
        foreach ($files as $file) {
            $res = $this->IsValidFile($file);
            if ($res !== true) {
                return $res;
            }
        }

        // di chuyển từng file vào dir vừa tạo
        foreach ($files as $file) {
            $extension = '';
            if ($file['type'] === 'image/jpeg') {
                $extension = '.jpeg';
            } else if ($file['type'] === 'image/png') {
                $extension = '.png';
            }

            $fileName = "img" . time() . $extension;

            // delay để tạo tên file
            sleep(1);

            // lưu lại dir
            $fileDirs[] = $uploadPath . "/" . $fileName;
            move_uploaded_file($file['tmp_name'], $uploadPath . "/" . $fileName);
        }

        return $fileDirs;
    }
    function IsValidFile($file)
    {

        // get extension
        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = false;


        if (isset($file["tmp_name"]) && !empty($file["tmp_name"])) {
            $check = getimagesize($file["tmp_name"]);
        }
        if ($check === false) {
            if (empty($file["name"])) {
                return "Vui lòng chọn file";
            } else {
                return "File không phải file ảnh";
            }
        }


        // Kiểm tra định dạng hình ảnh
        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        if (!in_array($file['type'], $allowedMimeTypes)) {
            return "File không đúng định dạng";
        }

        // Kiểm tra kích thước tệp tin (vd: tối đa 2MB)
        if ($file['size'] > 52428800) {
            return "File vượt quá kích thước 50MB";
        }

        // Kiểm tra lỗi khi upload
        if ($file['error'] > 0) {
            return "Có lỗi khi upload file";
        }

        return true;
    }
    function editProduct()
    {
        $response = [
            'success' => false,
            'message' => ''
        ];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if ($_POST == NULL) {
                $response['message'] = "POST is empty";
                echo json_encode($response);
                return;
            }
            $this->access = true;
            $changeImage = $_POST['ChangeImage'];
            $size_quantities = array(
                "S" => $_POST['S'],
                "M" => $_POST['M'],
                "L" => $_POST['L'],
                "XL" => $_POST['XL'],
                "XXL" => $_POST['XXL']
            );
            $size_quantities = array_map('trim', $size_quantities);

            $product_data = array(
                "product_code" => $_POST['product_code'],
                "product_name" => $_POST['product_name'],
                "product_price" => $_POST['product_price'],
                "category_id" => $_POST['category_id'],
                "product_color" => $_POST['color'],
                "product_description" => $_POST['product_description']
            );
            $product_data['product_description'] = htmlspecialchars($product_data['product_description']);
            $product_data = array_map('trim', $product_data);
            $product_data["size_quantities"] = $size_quantities;
            //var_dump($_POST['MaSanPham']);

            //$check = $this->ValidateProductData($product_data);
            $check = "validated";
            if ($check === "validated") {
                //$model = $this->model("Order");
                //$res = $model->CheckOrderNotDeliveredYet($product_data);
                $res = "None";
                if ($res === "None") {
                    $model = $this->model("Product");
                    $old_images = $model->LoadProductImages($product_data['product_code']);

                    if (is_array($old_images)) {

                        if ($old_images != null) {

                            if ($changeImage == "true") {

                                $uploadedFile = $_FILES["file"];
                                $fileNames = $this->UpLoadFiles($uploadedFile);
                            } else {
                                $fileNames = $old_images;
                            }
                            if (!is_array($fileNames)) {
                                echo $fileNames;
                            } else {
                                // thêm ảnh vào data
                                $product_data["product_images"] = $fileNames;

                                $err = $model->EditProduct($product_data, $changeImage);
                                if ($err != "done" && $changeImage == "true") {
                                    foreach ($fileNames as $each) {
                                        unlink($each);
                                    }
                                } else if ($err == "done" && $changeImage == "true") {
                                    // xóa hình cũ
                                    foreach ($old_images as $each) {
                                        unlink($each);
                                    }
                                }
                                $response['message'] = $err;
                                $response['success'] = true;

                            }
                        } else {
                            $response['message'] = "Ma san pham khong ton tai";
                        }
                    } else {
                        $response['message'] = $old_images;
                    }
                } else {
                    $response['message'] = $res;
                }

            } else {
                $response['success'] = $check;
            }
        }
        echo json_encode($response);
        return;
    }
    function deleteProduct()
    {
        $response = [
            'success' => false,
            'message' => ''
        ];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $product_data = array(
                "product_code" => $_POST['product_code'],
            );

            $product_data = array_map('trim', $product_data);
            
            //$check = $this->validateSpecialCharacter($product_data);
            $check = false;
            if ($check == false) {
                $model = $this->model("Order");
                //$res = $model->CheckOrderNotDeliveredYet($product_data);
                //$res === "None"
                $model = $this->model("Product");
                // lấy đường dẫn hình và xóa nó đi
                $images = $model->LoadProductImages($product_data['product_code']);
                if (is_array($images)) {
                    if ($images != null) {

                        $err = $model->DeleteProduct($product_data);

                        if ($err == "done") {
                            foreach ($images as $each) {
                                unlink($each);
                                $response['success'] = true;
                            }
                        }
                        $response['message'] = $err;
                    } else {
                        // var_dump($images);
                        $response['message']  = "Mã sản phẩm " . $product_data["product_code"] . " không tồn tại";
                    }
                } else {
                    $response['message']  = $images;
                }

            } else {
                $response['message'] ="Lỗi kí tự dặc biệt";
            }
        }
        echo json_encode($response);
        return;
    }
}
?>