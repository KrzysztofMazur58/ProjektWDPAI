<?php

require_once "src/repository/UserDetailsRepository.php";

if (isset($_COOKIE['user'])) {
    $userData = json_decode($_COOKIE['user'], true);

    if (isset($userData['id'])) {
        $userId = $userData['id'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Brak ID użytkownika w ciastku']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Brak ciastka użytkownika']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['consumedCalories'], $data['consumedProtein'], $data['consumedFat'], $data['consumedCarbs'])) {
    $calories = $data['consumedCalories'];
    $protein = $data['consumedProtein'];
    $fat = $data['consumedFat'];
    $carbs = $data['consumedCarbs'];

    $userDetailsRepository = new UserDetailsRepository();

    $updateSuccess = $userDetailsRepository->updateConsumedData($userId, $calories, $protein, $fat, $carbs);

    if ($updateSuccess) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Błąd przy aktualizacji danych']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Brak wymaganych danych']);
}



