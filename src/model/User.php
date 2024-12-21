<?php
// classes/User.php

class User {
    private $email;
    private $password;
    private $name;
    private $surname;

    public function __construct($email, $password, $name, $surname) {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
    }

    // Getter dla emaila
    public function getEmail() {
        return $this->email;
    }

    // Getter dla hasła
    public function getPassword() {
        return $this->password;
    }

    // Getter dla imienia
    public function getName() {
        return $this->name;
    }

    // Getter dla nazwiska
    public function getSurname() {
        return $this->surname;
    }
}
?>
