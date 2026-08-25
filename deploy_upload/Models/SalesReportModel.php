<?php
require_once __DIR__ . '/../Database/database.php';

class SalesReportModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // Define the method to fetch monthly sales data
    public function getMonthlySalesData($year = null) {
        if ($year === null) {
            $year = date("Y");  // Use the current year if no year is provided
        }

        $sql = "SELECT
                    MONTH(o.orderdate) AS month,
                    COUNT(oi.quantity) AS total_quantity
                FROM
                    orders o
                JOIN
                    orderitems oi ON o.id = oi.order_id
                WHERE
                    YEAR(o.orderdate) = ?
                GROUP BY
                    MONTH(o.orderdate)
                ORDER BY
                    month ASC";

        $result = $this->db->query($sql, [$year]);

        // Initialize all months to 0
        $monthlyData = array_fill(1, 12, 0);
        foreach ($result as $row) {
            $monthlyData[$row["month"]] = floatval($row["total_quantity"]);
        }

        return $monthlyData;
    }
}
?>
