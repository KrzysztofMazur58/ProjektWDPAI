<?php

require_once __DIR__ . '/../../DatabaseConnector.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/UserDetails.php';

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnector::getInstance()->getConnection();
    }

    public function save(User $user) {
        try {

            $email = $user->getEmail();
            $password = $user->getPassword();
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();

            $stmt = $this->db->prepare("INSERT INTO users (email, password, first_name, last_name) 
                                    VALUES (:email, :password, :first_name, :last_name) 
                                    RETURNING id");

            // Teraz przekazujemy zmienne do bindParam
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $user->setId($result['id']);

            $userId = $user->getId();

            $roleStmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, 1)");
            $roleStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $roleStmt->execute();

            session_start();
            $_SESSION['user_id'] = $user->getId();

        } catch (PDOException $e) {
            echo "Błąd przy zapisie użytkownika: " . $e->getMessage();
        }
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $user = new User($result['email'], $result['password'], $result['first_name'], $result['last_name']);
            $user->setId($result['id']);
            return $user;
        }

        return null;
    }

    public function getAllUsers() {
        try {
            $stmt = $this->db->prepare("SELECT id, first_name, last_name, email FROM users");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $users;
        } catch (PDOException $e) {
            echo "Błąd przy pobieraniu użytkowników: " . $e->getMessage();
            return [];
        }
    }

    public function deleteUserById(int $id)
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

}
