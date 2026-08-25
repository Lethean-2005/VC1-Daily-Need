<?php
class LocationModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAllLocations() {
        try {
            $result = $this->db->query("SELECT id, location_name FROM locations");
            $locations = $result->fetchAll(PDO::FETCH_ASSOC);
            error_log("LocationModel::getAllLocations - Fetched locations: " . print_r($locations, true));
            return $locations ?: [];
        } catch (Exception $e) {
            error_log("Error fetching locations in LocationModel: " . $e->getMessage());
            return [];
        }
    }
}