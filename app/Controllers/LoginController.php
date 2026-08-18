<?php
require_once '../app/Models/Usuario.php';

class LoginController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['usuario_admin'])) {
            header('Location: /');
            exit();
        }
        $this->view('login');
    }

    public function autenticar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = $_POST['senha'] ?? '';

        if ($email && $senha) {
            $usuarioModel = new Usuario();
            $user = $usuarioModel->buscarAdminPorEmail($email);

            if ($user && password_verify($senha, $user['senha'])) {
                $_SESSION['usuario_admin'] = [
                    'id' => $user['id'],
                    'nome' => $user['nome'],
                    'email' => $user['email']
                ];
                header('Location: /');
                exit();
            }
        }

        $_SESSION['erro_login'] = 'Credenciais inválidas ou permissão negada.';
        header('Location: /login');
        exit();
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['usuario_admin']);
        session_destroy();
        header('Location: /login');
        exit();
    }
}