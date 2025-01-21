<?php

require_once __DIR__ . '/../../DatabaseConnector.php';
require_once __DIR__ . '/../model/UserDetails.php';

class UserDetailsRepository {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnector::getInstance()->getConnection();
    }

    public function saveUserDetails(UserDetails $userDetails) {
        try {
            $stmt = $this->db->prepare('
        INSERT INTO user_details (user_id, gender, height, weight, age, activity_level_id)
        VALUES (:user_id, :gender, :height, :weight, :age, :activity_level_id)
        ON CONFLICT (user_id) DO UPDATE SET
        gender = EXCLUDED.gender,
        height = EXCLUDED.height,
        weight = EXCLUDED.weight,
        age = EXCLUDED.age,
        activity_level_id = EXCLUDED.activity_level_id
        ');

            $userId = $userDetails->getUserId();
            $gender = $userDetails->getGender();
            $height = $userDetails->getHeight();
            $weight = $userDetails->getWeight();
            $age = $userDetails->getAge();
            $activityLevelId = $userDetails->getActivityLevel();

            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':gender', $gender, PDO::PARAM_STR);
            $stmt->bindParam(':height', $height, PDO::PARAM_INT);
            $stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
            $stmt->bindParam(':age', $age, PDO::PARAM_INT);
            $stmt->bindParam(':activity_level_id', $activityLevelId, PDO::PARAM_INT);

            $stmt->execute();

        } catch (PDOException $e) {
            error_log('Błąd przy zapisie szczegółów użytkownika: ' . $e->getMessage());
        }
    }

    public function getUserDetails($userId) {
        $stmt = $this->db->prepare("SELECT * FROM user_details WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return new UserDetails(
                $result['user_id'],
                $result['gender'],
                $result['height'],
                $result['weight'],
                $result['age'],
                $result['activity_level_id'],
                $result['daily_calories'],
                $result['consumed_calories'],
                $result['daily_protein'],
                $result['consumed_protein'],
                $result['daily_fat'],
                $result['consumed_fat'],
                $result['daily_carbohydrates'],
                $result['consumed_carbohydrates']
            );
        }

        return null;
    }

    public function updateConsumedData($userId, $newCalories, $newProtein, $newFat, $newCarbohydrates) {
        try {
            if (!is_numeric($newCalories) || $newCalories < 0 ||
                !is_numeric($newProtein) || $newProtein < 0 ||
                !is_numeric($newFat) || $newFat < 0 ||
                !is_numeric($newCarbohydrates) || $newCarbohydrates < 0) {
                throw new InvalidArgumentException("Niepoprawne wartości spożycia.");
            }

            $updateStmt = $this->db->prepare("
            UPDATE user_details 
            SET consumed_calories = :consumed_calories,
                consumed_protein = :consumed_protein,
                consumed_fat = :consumed_fat,
                consumed_carbohydrates = :consumed_carbohydrates
            WHERE user_id = :user_id
        ");
            $updateStmt->bindParam(':consumed_calories', $newCalories, PDO::PARAM_INT);
            $updateStmt->bindParam(':consumed_protein', $newProtein, PDO::PARAM_INT);
            $updateStmt->bindParam(':consumed_fat', $newFat, PDO::PARAM_INT);
            $updateStmt->bindParam(':consumed_carbohydrates', $newCarbohydrates, PDO::PARAM_INT);
            $updateStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

            $updateStmt->execute();

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

}
