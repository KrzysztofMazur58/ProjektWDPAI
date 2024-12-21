<?php

require_once 'AppController.php';
require_once __DIR__ . '/../model/User.php';

class SecurityController extends AppController {
    private $users = [];

    public function __construct() {
        session_start(); // Rozpocznij sesję

        // Sprawdź, czy użytkownicy są zapisani w sesji
        if (!isset($_SESSION['users'])) {
            $_SESSION['users'] = [];
        }

        // Przypisz użytkowników z sesji do $this->users
        $this->users = &$_SESSION['users'];

    }

    public function getUsers() {
        return $this->users;
    }

    public function login() {
        if ($this->isGet()) {
            return $this->render('login');
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        if (isset($this->users[$email])) {
            $user = $this->users[$email];

            if (password_verify($password, $user->getPassword())) {
                $_SESSION['user'] = ['email' => $user->getEmail()];
                header('Location: /user_details');
                exit();
            }
        }

        return $this->render('login', ['error' => 'Invalid email or password.']);
    }

    public function register() {
        if ($this->isGet()) {
            return $this->render('register');
        }

        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $name = $_POST['first_name'];
        $surname = $_POST['last_name'];

        if ($password !== $confirmPassword) {
            return $this->render('register', ['error' => 'Passwords do not match!']);
        }

        if (isset($this->users[$email])) {
            return $this->render('register', ['error' => 'Email is already taken!']);
        }

        // Dodaj użytkownika do sesji
        $this->users[$email] = new User(
            $email,
            password_hash($password, PASSWORD_BCRYPT),
            $name,
            $surname
        );

        header("Location: /login");
        exit();
    }
}







