<?php

require_once 'AppController.php';
require_once __DIR__ . '/../../DatabaseConnector.php';
require_once __DIR__ . '/../repository/UserRepository.php';

class DashboardController extends AppController {

    private $userRepository;

    public function __construct() {

        $this->userRepository = new UserRepository();
    }

    public function dashboard() {
        if (!$this->isUserLoggedIn()) {
            header('Location: /login');
            exit();
        }

        $this->preventCaching();
        $this->refreshUserCookie();
        $userData = $this->getUserDataFromCookie();

        $this->render('dashboard', ['name' => $userData['first_name']]);
    }

    public function admin_dashboard() {
        if (!$this->isAdmin()) {
            header('Location: /login');
            exit();
        }

        $users = $this->userRepository->getAllUsers();
        $this->render('admin_dashboard', ['users' => $users]);
    }

    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $userId = intval($_POST['user_id']);

            $this->userRepository->deleteUserById($userId);

            header("Location: /admin_dashboard?success=UserDeleted");
            exit();
        }

        header("Location: /admin_dashboard?error=InvalidRequest");
        exit();
    }

    public function isAdmin() {
        $userData = $this->getUserDataFromCookie();
        return isset($userData['isAdmin']) && $userData['isAdmin'] === true;
    }

    public function logout() {

        setcookie('user', '', time() - 3600, "/");

        header('Location: /login');
        exit();
    }

    private function isUserLoggedIn() {
        if (isset($_COOKIE['user'])) {
            $userData = $this->getUserDataFromCookie();

            if (time() - $userData['timestamp'] > 1200) {
                $this->logout();
                return false;
            }

            return true;
        }
        return false;
    }

    private function preventCaching() {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }

    private function refreshUserCookie() {
        if (isset($_COOKIE['user'])) {
            $userData = json_decode($_COOKIE['user'], true);
            setcookie('user', json_encode([
                'id' => $userData['id'],
                'first_name' => $userData['first_name'],
                'timestamp' => time(),
                'isAdmin' => $userData['isAdmin']
            ]), time() + 1200, "/");
        }
    }

    private function getUserDataFromCookie() {
        return json_decode($_COOKIE['user'], true);
    }
}
