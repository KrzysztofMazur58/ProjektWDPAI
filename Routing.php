<?php

require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/UserController.php';

class Routing {
    public static function run($url) {
        $action = explode("/", $url)[0];  // Pierwszy element ścieżki URL
        $controller = null;

        // Obsługa tras
        if (in_array($action, ["login", ""])) {
            $controller = new SecurityController();
            $action = 'login';
        }

        if ($action === "register") {
            $controller = new SecurityController();
            $action = 'register';
        }

        if ($action === "dashboard") {
            $controller = new DashboardController();
            $action = 'dashboard';
        }

        if ($action === "user_details") {
            $controller = new UserController();
            $action = 'userDetails';
        }

        // Wywołaj odpowiednią metodę kontrolera
        if ($controller !== null) {
            $controller->$action();
        } else {
            echo "Page not found!";
        }
    }
}

