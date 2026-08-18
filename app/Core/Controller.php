<?php
class Controller {
    public function view($view, $data = []) {
        extract($data);
        $viewFile = '../app/Views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Erro: A View '{$view}' não foi encontrada.");
        }
    }
}