<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/UserDetailsRepository.php';
require_once __DIR__ . '/../model/UserDetails.php';

class UserController extends AppController {
    private $userDetailsRepository;

    public function __construct() {
        $this->userDetailsRepository = new UserDetailsRepository();
    }

    private function getUserId() {

        if (isset($_COOKIE['user'])) {
            $userData = json_decode($_COOKIE['user'], true);
            if (isset($userData['id'])) {
                return $userData['id'];
            }
            return null;
        } else {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            return $_SESSION['user_id'] ?? null;
        }
    }

    public function userDetails() {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->render('user_details', ['error' => 'User not authenticated.']);
        }

        if ($this->isGet()) {
            return $this->render('user_details');
        }

        $gender = $_POST['gender'] ?? null;
        $height = $_POST['height'] ?? null;
        $weight = $_POST['weight'] ?? null;
        $age = $_POST['age'] ?? null;
        $activityLevel = $_POST['activity_level'] ?? null;

        if ($this->validateUserDetails($gender, $height, $weight, $age, $activityLevel)) {

            $userDetails = new UserDetails($userId, $gender, $height, $weight, $age, $activityLevel);

            $this->userDetailsRepository->saveUserDetails($userDetails);

            $_SESSION = array();
            session_destroy();
            header('Location: /dashboard');
            exit();
        } else {

            return $this->render('user_details', ['error' => 'Please fill out all fields correctly.']);
        }
    }

    private function validateUserDetails($gender, $height, $weight, $age, $activityLevel) {

        if (empty($gender) || empty($height) || empty($weight) || empty($age) || empty($activityLevel)) {
            return false;
        }

        if (!is_numeric($height) || !is_numeric($weight) || !is_numeric($age)) {
            return false;
        }

        return true;
    }

    public function myProfile() {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->render('error', ['error' => 'User not authenticated.']);
        }

        $userDetails = $this->userDetailsRepository->findByUserId($userId);

        if ($userDetails) {
            return $this->render('my_profile', ['user' => $userDetails]);
        }

        return $this->render('error', ['error' => 'No profile found for this user.']);
    }

}