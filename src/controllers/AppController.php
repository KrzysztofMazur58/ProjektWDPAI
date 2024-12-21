<?php

class AppController {
    // Publiczna metoda render() umożliwia wywoływanie jej z innych kontrolerów

    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
 
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function render(string $template = null, array $variables = []) {
        $templatePath = 'public/views/' . $template . '.php';
        $output = 'File not found';
        
        // Sprawdzamy, czy plik istnieje, zanim spróbujemy go załadować
        if (file_exists($templatePath)) {
            extract($variables);
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }
        
        print $output;
    }
}
