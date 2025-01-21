<?php

class ApiConnector
{
    const API_URL = 'https://trackapi.nutritionix.com/v2/natural/nutrients';
    const API_ID = '15a2bfb1';
    const API_KEY = '88a03e59d16780fe54f929cebe617060';

    public function getMealData($mealName)
    {
        $postData = json_encode(['query' => $mealName]);

        $ch = curl_init(self::API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-app-id: ' . self::API_ID,
            'x-app-key: ' . self::API_KEY,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $data = json_decode($response, true);
        curl_close($ch);

        if (isset($data['foods'][0])) {
            $food = $data['foods'][0];

            return [
                'calories' => $food['nf_calories'] ?? 0,
                'protein' => $food['nf_protein'] ?? 0,
                'fat' => $food['nf_total_fat'] ?? 0,
                'carbohydrates' => $food['nf_total_carbohydrate'] ?? 0,
                'serving_weight_grams' => $food['serving_weight_grams'] ?? 0
            ];
        }

        return [
            'calories' => 0,
            'protein' => 0,
            'fat' => 0,
            'carbohydrates' => 0
        ];
    }
}

