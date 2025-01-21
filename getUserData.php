<?php

require_once "src/repository/UserDetailsRepository.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['user'])) {
    echo json_encode(['error' => 'Nie znaleziono identyfikatora użytkownika.']);
    exit;
}

$userData = json_decode($_COOKIE['user'], true);

if (isset($userData['id'])) {
    $userId = $userData['id'];

    $userDetailsRepository = new UserDetailsRepository();

    try {
        $userDetails = $userDetailsRepository->getUserDetails($userId);

        if ($userDetails) {
            $userData = [
                'weight' => $userDetails->getWeight(),
                'height' => $userDetails->getHeight(),
                'age' => $userDetails->getAge(),
                'activityLevel' => $userDetails->getActivityLevel(),
                'gender' => $userDetails->getGender(),
                'daily_calories' => $userDetails->getDailyCalories(),
                'consumed_calories' => $userDetails->getConsumedCalories(),
                'daily_protein' => $userDetails->getDailyProtein(),
                'consumed_protein' => $userDetails->getConsumedProtein(),
                'daily_fat' => $userDetails->getDailyFat(),
                'consumed_fat' => $userDetails->getConsumedFat(),
                'daily_carbs' => $userDetails->getDailyCarbs(),
                'consumed_carbs' => $userDetails->getConsumedCarbs(),
            ];

            echo json_encode($userData);
        } else {
            echo json_encode(['error' => 'Nie znaleziono danych użytkownika.']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Wystąpił błąd podczas pobierania danych użytkownika.']);
    }
} else {
    echo json_encode(['error' => 'Nie znaleziono identyfikatora użytkownika w ciasteczku.']);
}