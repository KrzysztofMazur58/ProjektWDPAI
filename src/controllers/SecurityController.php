<?php
ob_start();
require_once 'AppController.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/UserSecurityRepository.php';

class SecurityController extends AppController {
    private $userRepository;
    private $userSecurityRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userSecurityRepository = new UserSecurityRepository();
    }

    public function login() {
        if ($this->isGet()) {
            return $this->render('login');
        }

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$email || !$password) {
            return $this->render('login', ['error' => 'Please enter both email and password.']);
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user && password_verify($password, $user->getPassword())) {
            return $this->handleSuccessfulLogin($user);
        }

        return $this->render('login', ['error' => 'Invalid email or password.']);
    }

    public function register() {
        if ($this->isGet()) {
            return $this->render('register');
        }

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;
        $name = $_POST['first_name'] ?? null;
        $surname = $_POST['last_name'] ?? null;

        if ($password !== $confirmPassword) {
            return $this->render('register', ['error' => 'Passwords do not match!']);
        }

        if ($this->userRepository->findByEmail($email)) {
            return $this->render('register', ['error' => 'Email is already taken!']);
        }

        $user = new User(
            $email,
            password_hash($password, PASSWORD_BCRYPT),
            $name,
            $surname
        );

        $this->userRepository->save($user);

        header("Location: /user_details");
        exit();
    }

    private function handleSuccessfulLogin(User $user) {
        $isAdmin = $this->checkIfUserIsAdmin($user);

        setcookie('user', json_encode([
            'id' => $user->getId(),
            'first_name' => $user->getFirstName(),
            'timestamp' => time(),
            'isAdmin' => $isAdmin
        ]), time() + 1200, "/");

        header('Location: /dashboard');
        exit();
    }

    private function checkIfUserIsAdmin(User $user) {
        $role = $this->userSecurityRepository->getUserRole($user->getId());
        return $role === 'admin';
    }
}
