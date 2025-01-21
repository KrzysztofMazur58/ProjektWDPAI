<?php

require_once 'ApiConnector.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Odbierz dane z frontendu
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['query'])) {
        $mealName = $data['query'];

        // Utwórz instancję ApiConnector i pobierz dane żywieniowe
        $apiConnector = new ApiConnector();
        $mealData = $apiConnector->getMealData($mealName);

        // Zwróć odpowiedź w formacie JSON
        echo json_encode($mealData);
    } else {
        echo json_encode(['error' => 'Brak nazwy posiłku w zapytaniu']);
    }
}

