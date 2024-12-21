<?php

require_once 'AppController.php';

class UserController extends AppController {
    public function userDetails() {
        if ($this->isGet()) {
            // Wyświetlenie formularza użytkownika
            return $this->render('user_details');
        }

        // Pobranie danych z formularza
        $gender = $_POST['gender'];
        $height = $_POST['height'];
        $weight = $_POST['weight'];
        $age = $_POST['age'];
        $activityLevel = $_POST['activity_level'];

        // Walidacja danych
        if (empty($gender) || empty($height) || empty($weight) || empty($age) || empty($activityLevel)) {
            return $this->render('user_details', ['error' => 'Please fill out all fields.']);
        }

        // Możliwość zapisania danych do bazy danych
        // np. $userRepository->saveUserDetails($gender, $height, $weight, $age, $activityLevel);

        // Zakładając, że zapisanie danych zakończyło się sukcesem, przekierowanie do dashboard
        header('Location: /dashboard');
        exit();
    }
}

