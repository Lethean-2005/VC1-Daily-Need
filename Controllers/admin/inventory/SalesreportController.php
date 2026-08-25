<?php

 // Ensure the correct path to the model file
    require_once  'Models/SalesReportModel.php';  // Adjust the path as necessary
                
// Inherit from the base admin controller
    class SalesreportController extends BaseAdminController {
        private $salesReportModel;
                
        public function __construct() {
     // Initialize the model
        $this->salesReportModel = new SalesReportModel();
        }
                
    // Method to render the view for the sales report
    public function salesreport() {
        $monthlySalesData = $this->salesReportModel->getMonthlySalesData();
        $this->view('admin/inventory/salesReport', ['monthlySalesData' => $monthlySalesData]);
    }
    

    // Method to get sales report data
    public function getSalesReport() {
        // Fetch the sales data using the model
        $Salesdata = $this->salesReportModel->getSalesData();
        // You can process the data here or pass it to a view
        return $Salesdata;
    }
}                
?>
               