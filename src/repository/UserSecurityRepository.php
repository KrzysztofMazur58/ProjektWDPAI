<?php

require_once __DIR__ . '/../../DatabaseConnector.php';

class UserSecurityRepository {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnector::getInstance()->getConnection();
    }

    public function getUserRole($userId) {
        // Zmodyfikowane zapytanie z priorytetem roli admin dla PostgreSQL
        $stmt = $this->db->prepare("SELECT r.role_name 
                                 FROM roles r
                                 JOIN user_roles ur ON r.id = ur.role_id
                                 WHERE ur.user_id = :user_id
                                 ORDER BY CASE 
                                            WHEN r.role_name = 'admin' THEN 1
                                            ELSE 2
                                          END");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($role) {
            return $role['role_name'];
        }

        return null;
    }
}
