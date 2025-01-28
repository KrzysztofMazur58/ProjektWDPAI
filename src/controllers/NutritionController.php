<?php

require_once __DIR__ . '/../repository/UserDetailsRepository.php';
require_once __DIR__ . '/../repository/NutritionTipsRepository.php';
require_once __DIR__ . '/../../ApiConnector.php';

class NutritionController {

    public function getUserData() {
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
    }

    public function updateUserData() {
        header('Content-Type: application/json');

        if (!isset($_COOKIE['user'])) {
            echo json_encode(['error' => 'Brak ciastka użytkownika']);
            exit;
        }

        $userData = json_decode($_COOKIE['user'], true);

        if (isset($userData['id'])) {
            $userId = $userData['id'];
        } else {
            echo json_encode(['success' => false, 'message' => 'Brak ID użytkownika w ciastku']);
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
    }

    public function getMealData() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['query'])) {
            $mealName = $data['query'];

            $apiConnector = new ApiConnector();
            $mealData = $apiConnector->getMealData($mealName);

            echo json_encode($mealData);
        } else {
            echo json_encode(['error' => 'Brak nazwy posiłku w zapytaniu']);
        }
    }

    public function getNutritionTip() {
        header('Content-Type: application/json');

        if (isset($_GET['hour'])) {
            $hour = (int)$_GET['hour'];

            $nutritionTipsRepo = new NutritionTipsRepository();

            $tip = $nutritionTipsRepo->getRandomTipByTime($hour);

            if ($tip) {
                echo json_encode($tip);
            } else {
                echo json_encode(['error' => 'No tip found for this hour.']);
            }
        } else {
            echo json_encode(['error' => 'No hour parameter provided.']);
        }
    }
}

