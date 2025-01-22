<?php

require_once "src/repository/NutritionTipsRepository.php";

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
