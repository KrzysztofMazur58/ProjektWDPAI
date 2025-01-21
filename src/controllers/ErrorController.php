<?php

require_once 'AppController.php';

class ErrorController extends AppController {

    public function error404() {

        $this->render('errors/404');
    }
}