<?php

class UserDetails {
    private $userId;
    private $gender;
    private $height;
    private $weight;
    private $age;
    private $activityLevel;
    private $dailyCalories;
    private $consumedCalories;

    private $dailyProtein;
    private $consumedProtein;

    private $dailyFat;
    private $consumedFat;

    private $dailyCarbs;
    private $consumedCarbs;

    public function __construct(
        $userId,
        $gender,
        $height,
        $weight,
        $age,
        $activityLevel,
        $dailyCalories = 0,
        $consumedCalories = 0,
        $dailyProtein = 0,
        $consumedProtein = 0,
        $dailyFat = 0,
        $consumedFat = 0,
        $dailyCarbs = 0,
        $consumedCarbs = 0
    ) {
        $this->userId = $userId;
        $this->gender = $gender;
        $this->height = $height;
        $this->weight = $weight;
        $this->age = $age;
        $this->activityLevel = $activityLevel;
        $this->dailyCalories = $dailyCalories;
        $this->consumedCalories = $consumedCalories;

        $this->dailyProtein = $dailyProtein;
        $this->consumedProtein = $consumedProtein;
        $this->dailyFat = $dailyFat;
        $this->consumedFat = $consumedFat;
        $this->dailyCarbs = $dailyCarbs;
        $this->consumedCarbs = $consumedCarbs;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getGender() {
        return $this->gender;
    }

    public function getHeight() {
        return $this->height;
    }

    public function getWeight() {
        return $this->weight;
    }

    public function getAge() {
        return $this->age;
    }

    public function getActivityLevel() {
        return $this->activityLevel;
    }

    public function getDailyCalories() {
        return $this->dailyCalories;
    }

    public function getConsumedCalories() {
        return $this->consumedCalories;
    }

    public function getDailyProtein() {
        return $this->dailyProtein;
    }

    public function getConsumedProtein() {
        return $this->consumedProtein;
    }

    public function getDailyFat() {
        return $this->dailyFat;
    }

    public function getConsumedFat() {
        return $this->consumedFat;
    }

    public function getDailyCarbs() {
        return $this->dailyCarbs;
    }

    public function getConsumedCarbs() {
        return $this->consumedCarbs;
    }

    public function setGender($gender) {
        $this->gender = $gender;
    }

    public function setHeight($height) {
        $this->height = $height;
    }

    public function setWeight($weight) {
        $this->weight = $weight;
    }

    public function setAge($age) {
        $this->age = $age;
    }

    public function setActivityLevel($activityLevel) {
        $this->activityLevel = $activityLevel;
    }

    public function setDailyCalories($dailyCalories) {
        $this->dailyCalories = $dailyCalories;
    }

    public function setConsumedCalories($consumedCalories) {
        $this->consumedCalories = $consumedCalories;
    }

    public function setDailyProtein($dailyProtein) {
        $this->dailyProtein = $dailyProtein;
    }

    public function setConsumedProtein($consumedProtein) {
        $this->consumedProtein = $consumedProtein;
    }

    public function setDailyFat($dailyFat) {
        $this->dailyFat = $dailyFat;
    }

    public function setConsumedFat($consumedFat) {
        $this->consumedFat = $consumedFat;
    }

    public function setDailyCarbs($dailyCarbs) {
        $this->dailyCarbs = $dailyCarbs;
    }

    public function setConsumedCarbs($consumedCarbs) {
        $this->consumedCarbs = $consumedCarbs;
    }
}



