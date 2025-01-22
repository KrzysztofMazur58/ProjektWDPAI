<?php

require_once 'ApiConnector.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

