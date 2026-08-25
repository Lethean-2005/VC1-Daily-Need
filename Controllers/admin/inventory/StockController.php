<?php
require_once 'Models/StockModel.php';

class StockController extends BaseAdminController {
    private $stockModel;

    public function __construct() {
        $this->stockModel = new StockModel();
    }

    public function stock() {
        $this->view('admin/inventory/stock');
    }

    public function stockIn() {
        $data = [
            'stockHistory' => $this->stockModel->getStockHistory('in')
        ];
        $this->view('admin/inventory/stocks/stockIn', $data);
    }

    public function stockOut() {
        $data = [
            'stockHistory' => $this->stockModel->getStockHistory('out'),
            'stockLevels' => $this->stockModel->getStockLevels()
        ];
        $this->view('admin/inventory/stocks/stockOut', $data);
    }
}
?>