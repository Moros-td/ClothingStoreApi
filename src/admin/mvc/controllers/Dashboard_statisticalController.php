<?php
include_once "./mvc/models/StatisticalModel/Statistical.php";

class Dashboard_statisticalController extends Controller {
    function getOrderDetails() {
        $model = $this->model("Statistical");
        $data = $model->getOrderDetails();
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    function getRevenueByMonth() {
        $model = $this->model("Statistical");
        $data = $model->getRevenueByMonth();
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    function getRevenueByYear() {
        $model = $this->model("Statistical");
        $data = $model->getRevenueByYear();
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    function getTopSellingProducts() {
        $model = $this->model("Statistical");
        $data = $model->getTopSellingProducts();
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
?>
