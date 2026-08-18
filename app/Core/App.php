<?php

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        // 1. Inicia a sessão se ainda não tiver sido iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $url = $this->parseUrl();

        // 2. Define o controller solicitado (ou usa o padrão 'home')
        $requestedController = isset($url[0]) ? strtolower($url[0]) : 'home';

        // 3. Sistema de Proteção por Login
        // Lista de rotas públicas que NÃO precisam de autenticação
        $rotasPublicas = ['login', 'api']; 

        // Se o usuário NÃO estiver logado e tentar acessar uma rota privada, manda para o Login
        if (!isset($_SESSION['usuario_logado']) && !in_array($requestedController, $rotasPublicas)) {
            header('Location: dash-trabalheconosco/login');
            exit();
        }

        // 4. Carregamento Dinâmico do Controller
        if (isset($url[0]) && file_exists('../app/Controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controller = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        require_once '../app/Controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller();

        // 5. Verificação do Método (Ação do Controller)
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        // 6. Organização dos Parâmetros e Execução
        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl() {
        // 1. Tenta pegar a URL reescrita pelo .htaccess (Comportamento do Apache)
        if (isset($_GET['url']) && !empty($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }

        // 2. Fallback: Lê direto da URI (Comportamento do servidor embutido: php -S)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, '/');
        
        if (!empty($uri)) {
            return explode('/', filter_var($uri, FILTER_SANITIZE_URL));
        }

        // 3. Retorna array vazio caso esteja na raiz do site
        return [];
    }
}