<?php
require_once '../app/Models/Usuario.php';

class LoginController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Se já estiver logado, manda direto para a home
        if (isset($_SESSION['usuario_admin'])) {
            header('Location: ' . BASE_URL . '/');
            exit();
        }
        
        $this->view('login');
    }

    public function autenticar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?: trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $usuarioModel = new Usuario();
        $user = $usuarioModel->buscarAdminPorEmail($email);

        // Verifica se o usuário existe e se a senha está correta
        if ($user && password_verify($senha, $user['senha'])) {
            
            // Cria a sessão do usuário logado guardando os dados principais
            $_SESSION['usuario_admin'] = [
                'id' => $user['id'],
                'nome' => $user['nome'],
                'email' => $user['email']
            ];
            
            // Redireciona para o painel principal (Home)
            header('Location: ' . BASE_URL . '/');
            exit();
            
        } else {
            // Falha no login: envia erro e redireciona de volta ao formulário
            $_SESSION['erro_login'] = "Credenciais inválidas ou permissão negada.";
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Limpa a sessão e desloga o usuário
        unset($_SESSION['usuario_admin']);
        session_destroy();
        
        // Redireciona para o login
        header('Location: ' . BASE_URL . '/login');
        exit();
    }
}