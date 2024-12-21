<?php

require_once 'Routing.php';

$path = $_SERVER['REQUEST_URI'];  // Pobieramy pełną ścieżkę URL

$route = parse_url($path, PHP_URL_PATH);  // Wyciągamy ścieżkę URL
$route = trim($route, '/');  // Usuwamy początkowe i końcowe ukośniki

Routing::run($route);  // Uruchamiamy routing na podstawie URL

