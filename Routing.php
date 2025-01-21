<?php

require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/UserController.php';
require_once 'src/controllers/ErrorController.php';

class Routing {
    private static $routes = [
        "" => [SecurityController::class, 'login'],
        "login" => [SecurityController::class, 'login'],
        "register" => [SecurityController::class, 'register'],
        "dashboard" => [DashboardController::class, 'dashboard'],
        "user_details" => [UserController::class, 'userDetails'],
        "logout" => [DashboardController::class, 'logout'],
        "admin_dashboard" => [DashboardController::class, 'admin_dashboard'],
        "delete_user" => [DashboardController::class, 'deleteUser'],
    ];

    public static function run($url) {
        $action = explode("/", $url)[0];

        if (array_key_exists($action, self::$routes)) {
            [$controllerClass, $method] = self::$routes[$action];
            $controller = new $controllerClass();
            $controller->$method();
        } else {

            $errorController = new ErrorController();
            $errorController->error404();
        }
    }
}




