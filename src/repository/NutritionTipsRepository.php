<?php

require_once __DIR__ . '/../../DatabaseConnector.php';

class NutritionTipsRepository {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnector::getInstance()->getConnection();
        error_log("Database connection initialized.");
    }

    public function getRandomTipByTime($hour) {
        error_log("Fetching tip for hour: $hour");

        $stmt = $this->db->prepare("
            SELECT tip, benefit 
            FROM nutrition_tips 
            WHERE hour_start <= :hour AND hour_end > :hour
            ORDER BY RANDOM() 
            LIMIT 1
        ");

        $stmt->bindParam(':hour', $hour, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            error_log("Tip fetched: " . json_encode($result));
            return $result;
        } else {
            error_log("No tip found in database for hour: $hour");
            return null;
        }
    }
}

