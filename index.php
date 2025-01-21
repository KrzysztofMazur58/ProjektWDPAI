<?php

require_once 'Routing.php';

$path = $_SERVER['REQUEST_URI'];

$route = parse_url($path, PHP_URL_PATH);
$route = trim($route, '/');

Routing::run($route);