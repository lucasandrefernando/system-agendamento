<?php
class AuthController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    public function loginForm()
    {
        // Se já estiver logado, redireciona para o dashboard
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /projeto-agendamento/public/dashboard');
            exit;
        }

        ob_start();
        require_once __DIR__ . '/../views/auth/login.php';
        $conteudo = ob_get_clean();

        require_once __DIR__ . '/../views/layouts/auth.php';
    }

    public function login()
    {
        $usuario = $_POST['usuario'] ?? '';
        $senha = $_POST['senha'] ?? '';

        // Validação básica
        if (empty($usuario) || empty($senha)) {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Por favor, preencha todos os campos.'
            ];
            header('Location: /projeto-agendamento/public/');
            exit;
        }

        // Tentativa de autenticação
        $usuarioAutenticado = $this->usuarioModel->autenticar($usuario, $senha);

        if ($usuarioAutenticado) {
            // Armazenar informações do usuário na sessão
            $_SESSION['usuario_id'] = $usuarioAutenticado['id'];
            $_SESSION['usuario_nome'] = $usuarioAutenticado['nome'];
            $_SESSION['usuario_email'] = $usuarioAutenticado['email'];
            $_SESSION['usuario_usuario'] = $usuarioAutenticado['usuario'];
            $_SESSION['usuario_tipo'] = $usuarioAutenticado['tipo_usuario'];

            // Registrar log de login
            $this->registrarLog($usuarioAutenticado['id'], 'LOGIN', 'Login no sistema');

            // Redirecionar para o dashboard
            header('Location: /projeto-agendamento/public/dashboard');
        } else {
            $_SESSION['mensagem'] = [
                'tipo' => 'erro',
                'texto' => 'Usuário ou senha inválidos.'
            ];
            header('Location: /projeto-agendamento/public/');
        }
        exit;
    }

    public function logout()
    {
        // Registrar log de logout
        if (isset($_SESSION['usuario_id'])) {
            $this->registrarLog($_SESSION['usuario_id'], 'LOGOUT', 'Logout do sistema');
        }

        // Destruir a sessão
        session_unset();
        session_destroy();

        // Redirecionar para a página de login
        header('Location: /projeto-agendamento/public/');
        exit;
    }

    private function registrarLog($usuarioId, $acao, $descricao)
    {
        // Verificar se existe a tabela de logs
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                INSERT INTO logs_atividades (usuario_id, acao, descricao, entidade, entidade_id)
                VALUES (:usuario_id, :acao, :descricao, 'usuario', :entidade_id)
            ");

            $stmt->bindParam(':usuario_id', $usuarioId);
            $stmt->bindParam(':acao', $acao);
            $stmt->bindParam(':descricao', $descricao);
            $stmt->bindParam(':entidade_id', $usuarioId);

            $stmt->execute();
        } catch (PDOException $e) {
            // Ignorar erros (a tabela pode não existir ainda)
            error_log("Erro ao registrar log: " . $e->getMessage());
        }
    }
}
